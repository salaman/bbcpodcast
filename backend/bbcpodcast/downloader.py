from __future__ import division

import os
import errno
import subprocess
import logging
import swiftclient
import tempfile
import time

from .config import config
from .service import Service


class FileReadWrapper(object):

    def __init__(self, file_handle):
        self._fin = file_handle
        self._size = os.stat(file_handle.name).st_size
        self._logger = logging.getLogger(__name__)

        self._update_interval = 15
        self._last_update = None

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self._logger.info("Upload complete!")

    def read(self, size=None):
        currtime = time.time()

        if self._last_update is None or (currtime - self._last_update) > self._update_interval:
            self._last_update = currtime
            self._logger.info("'%s': %d%% complete\r" % (os.path.basename(self._fin.name),
                                                         round(self._fin.tell() / self._size * 100, 2)))

        return self._fin.read(size)

    def __getattr__(self, item):
        return getattr(self._fin, item)


class Downloader(object):

    def __init__(self, session):
        self.media_storage = config.get("app", "media_storage")

        self._service = Service()
        self._session = session
        self._logger = logging.getLogger(__name__)

    def download_episode(self, entry):
        session = self._session

        output = self.media_storage.format(programme=entry.programme.programme_id,
                                           episode=entry.entry_id,
                                           mediator=entry.mediator_id,
                                           extension="m4a")

        streams = self._service.get_stream_urls(entry.mediator_id)
        stream = self._select_best_stream(streams)

        # Check to make sure there was no error
        if stream is None:
            raise Exception("Error parsing streams: %s", streams)

        # Dump RTMP stream into temporary file
        rtmp_tmp = tempfile.NamedTemporaryFile(suffix='.flv')
        self.dump_rtmp(stream, rtmp_tmp.name)

        # Convert temporary file into m4a
        ffmpeg_tmp = tempfile.NamedTemporaryFile(suffix='.m4a')
        self.convert_file(rtmp_tmp.name, ffmpeg_tmp.name)
        rtmp_tmp.close()

        # Upload file
        url = self.upload_file(ffmpeg_tmp, output)

        entry.status = 1
        entry.service = stream['service']
        entry.bitrate = int(stream['bitrate'])
        entry.size = os.path.getsize(ffmpeg_tmp.name)
        entry.url = url

        ffmpeg_tmp.close()
        session.commit()

        return True

    def dump_rtmp(self, stream, output):
        # Attempt to create directory
        self._create_media_dir(output)

        rtmpdump_path = config.get("app", "rtmpdump_path")

        # Check for required executables in PATH
        if self.which(rtmpdump_path) is None:
            raise Exception("Can't find ffmpeg, given location: '%s'" % rtmpdump_path)

        rtmp_command = [
            rtmpdump_path,
            "-r",
            "rtmp://%s:1935/" % stream['connection'][0]['server'],
            "-a",
            "%s?%s" % (stream['connection'][0]['application'], stream['connection'][0]['authString']),
            "-y",
            stream['connection'][0]['identifier'],
            "-o",
            output
        ]

        self._logger.info("Using rtmpdump to dump stream to '%s'" % output)
        self._run_command(rtmp_command)

        # Remove temporary flv dump
        #os.remove(tempfile)

        return True

    def convert_file(self, path, output):
        # Attempt to create directory
        self._create_media_dir(output)

        ffmpeg_path = config.get("app", "ffmpeg_path")

        if self.which(ffmpeg_path) is None:
            raise Exception("Can't find rtmpdump, given location: '%s'" % ffmpeg_path)

        ffmpeg_command = [
            ffmpeg_path,
            "-i",
            path,
            "-vn",
            "-acodec",
            "copy",
            "-analyzeduration",
            "100000000",
            "-movflags",
            "faststart",
            "-y",
            output
        ]

        self._logger.info("Converting '%s' to '%s' using ffmpeg..." % (path, output))
        self._run_command(ffmpeg_command)

        return True

    def upload_file(self, handle, output):
        self._logger.info("Uploading file '%s' to swift ('%s')..." % (handle.name, output))

        client = swiftclient.client.Connection(authurl=config.get("swift", "auth_url"),
                                               user=config.get("swift", "username"),
                                               key=config.get("swift", "password"),
                                               tenant_name=config.get("swift", "tenant_name"),
                                               auth_version="2",
                                               os_options={'region_name': config.get("swift", "region_name")})

        with FileReadWrapper(handle) as f:
            etag = client.put_object(config.get("swift", "container"), output, f, chunk_size=10485760)

        client.close()

        url = "%s/%s" % (config.get("swift", "public_url"), output)

        return url

    def _create_media_dir(self, path):
        basepath = os.path.dirname(path)

        try:
            os.makedirs(basepath)
        except OSError as e:
            if e.errno == errno.EEXIST and os.path.isdir(basepath):
                pass
            else:
                raise

    def _select_best_stream(self, streams):
        # Simple search to find highest bitrate media in case more than one is returned
        highest_bitrate_index = None

        for index, media in enumerate(streams['media']):
            if highest_bitrate_index is None or int(media['bitrate']) > int(streams['media'][highest_bitrate_index]['bitrate']):
                highest_bitrate_index = index

        if highest_bitrate_index is None:
            raise Exception("Unable to parse bitrate from media")

        media = streams['media'][highest_bitrate_index]

        self._logger.info("Got stream '%s', bitrate %s", media['service'], media['bitrate'])

        return media

    def _run_command(self, command):
        subprocess.check_call(command)

    # https://stackoverflow.com/questions/377017/test-if-executable-exists-in-python/377028#377028
    def which(self, program):
        import os
        def is_exe(fpath):
            return os.path.isfile(fpath) and os.access(fpath, os.X_OK)

        fpath, fname = os.path.split(program)
        if fpath:
            if is_exe(program):
                return program
        else:
            for path in os.environ["PATH"].split(os.pathsep):
                path = path.strip('"')
                exe_file = os.path.join(path, program)
                if is_exe(exe_file):
                    return exe_file

        return None
