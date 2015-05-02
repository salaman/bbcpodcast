import logging

from bbcpodcast.ripper import Ripper
from bbcpodcast.db import Session
from bbcpodcast.downloader import Downloader
from bbcpodcast.service import Service
from bbcpodcast.celery import app
from bbcpodcast.main import fetch_new
from bbcpodcast.tasks import download_episode, update_programme_metadata, update_programme

logging.basicConfig(level=logging.INFO)
logging.getLogger("requests.packages.urllib3.connectionpool").setLevel(level=logging.WARN)

session = Session()

ripper = Ripper(session)
downloader = Downloader(session)

#print fetch_new()
#print downloader.upload_file("test.bin")
download_episode.delay(1)
#update_programme_metadata.delay(1)
#update_programme.delay(1)

session.close()
Session.remove()
