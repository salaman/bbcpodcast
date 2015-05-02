import dateutil.parser
import requests
import logging

from .models.entry import Entry
from .models.programme import Programme


class Service(object):

    def __init__(self):
        self._logger = logging.getLogger(__name__)

    def get_episode_ids_for_programme(self, programme_id):
        url = "http://www.bbc.co.uk/programmes/%s/episodes/player.json" % programme_id

        # Get programme_set of programme id
        self._logger.info("Making request to '%s'", url)
        r = requests.get(url, headers={"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.20 Safari/537.36"})
        r.raise_for_status()
        response = r.json()

        # Make sure episodes array exists
        if 'episodes' not in response:
            raise Exception("Invalid programme '%s': no 'episodes' key", programme_id)

        episodes = []

        # Parse linked episodes
        for episode in response['episodes']:
            if 'programme' not in episode:
                self._logger.warning("Found broken episode entry in programme '%s': no 'programme' key", programme_id)
                continue

            if 'pid' not in episode['programme']:
                self._logger.warning("Found broken episode entry in programme '%s': no 'pid' key", programme_id)
                continue

            episodes.append(episode['programme']['pid'])

        self._logger.info("Found %s episode(s) for programme '%s': %s", len(episodes), programme_id, episodes)

        return episodes

    def get_episode(self, episode_id):
        url = "http://www.bbc.co.uk/programmes/%s.json" % episode_id

        # Get playlist for episode id
        self._logger.info("Making request to '%s'", url)
        r = requests.get(url, headers={"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.20 Safari/537.36"})
        r.raise_for_status()
        response = r.json()

        if 'programme' not in response:
            raise Exception("Invalid episode %s: no 'programme' key" % episode_id)

        episode = response['programme']

        if 'type' not in episode or episode['type'] != "episode":
            raise Exception("Invalid episode %s: given pid is not an episode" % episode_id)

        if 'versions' not in episode:
            raise Exception("Invalid episode %s: no 'versions' key" % episode_id)

        versions = episode['versions']

        if len(versions) == 0:
            raise Exception("Invalid episode %s: no versions found" % episode_id)

        entry = Entry(entry_id=episode_id)

        entry.mediator_id = versions[0]['pid']
        entry.title = episode['title']
        entry.subtitle = episode['short_synopsis']
        entry.description = episode['long_synopsis'] if episode['medium_synopsis'] is None else episode['medium_synopsis']
        entry.duration = versions[0]['duration']
        entry.broadcast_at = dateutil.parser.parse(episode['first_broadcast_date']).utcnow()

        if 'image' in episode and episode['image'] is not None:
            entry.image = "https://ichef.bbc.co.uk/images/ic/944x531/%s.jpg" % episode['image']['pid']

        self._logger.info("Got episode: %s", entry)

        return entry

    def get_programme(self, programme_id):
        url = "http://www.bbc.co.uk/programmes/%s.json" % programme_id

        # Get playlist for episode id
        self._logger.info("Making request to '%s'", url)
        r = requests.get(url, headers={"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.20 Safari/537.36"})
        r.raise_for_status()
        response = r.json()

        if 'programme' not in response:
            raise Exception("Invalid programme %s: no 'programme' key" % programme_id)

        programme_data = response['programme']

        if 'type' not in programme_data or programme_data['type'] != "brand":
            raise Exception("Invalid programme %s: given pid is not a brand" % programme_id)

        # Note: image URLs (all the same):
        # - http://www.bbc.co.uk/iplayer/images/progbrand/b01d76m6_512_288.jpg
        # - http://ichef.bbci.co.uk/images/ic/512x288/legacy/brand/b01d76m6.jpg
        # - http://ichef.bbci.co.uk/images/ic/512x288/p01m61x2.jpg

        programme = Programme(programme_id=programme_id)

        programme.title = programme_data['title']
        programme.description = programme_data['long_synopsis'] if programme_data['medium_synopsis'] is None else programme_data['medium_synopsis']
        programme.image = "http://ichef.bbci.co.uk/images/ic/944x531/%s.jpg" % programme_data['image']['pid']

        self._logger.info("Got programme: %s", programme)

        return programme

    def get_stream_urls(self, mediator_id):
        mediaset = "pc"
        protocol = "rtmp"

        proxies = {
            "http": "http://lynx.chrs.pw:3128",
        }

        url = "http://open.live.bbc.co.uk/mediaselector/5/select/version/2.0/vpid/%s/format/json/mediaset/%s/proto/%s" \
              % (mediator_id, mediaset, protocol)

        self._logger.info("Using %s/%s for mediator id '%s'", mediaset, protocol, mediator_id)

        # Get media URLs
        r = requests.get(url, proxies=proxies, headers={"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.20 Safari/537.36"})
        r.raise_for_status()
        json = r.json()

        return json
