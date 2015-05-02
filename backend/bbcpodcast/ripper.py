import logging

from .models.entry import Entry
from .service import Service


class Ripper(object):

    def __init__(self, session):
        self._service = Service()
        self._session = session
        self._logger = logging.getLogger(__name__)

    def fetch_new_episodes_for_programme(self, programme):
        self._logger.info("Getting episodes for programme: %s", programme)
        episode_ids = self._service.get_episode_ids_for_programme(programme.programme_id)

        session = self._session
        episodes = []

        # Iterate over all fetched episodes
        for episode_id in episode_ids:
            # If this is a new episode, fetch the episode info
            if session.query(Entry).filter(Entry.entry_id == episode_id).count() == 0:
                entry = self._service.get_episode(episode_id)
                entry.programme_id = programme.id
                entry.status = 0
                episodes.append(entry)

        session.add_all(episodes)
        session.commit()

        return episodes

    def update_programme_metadata(self, programme):
        session = self._session

        new_programme = self._service.get_programme(programme.programme_id)

        programme.title = new_programme.title
        programme.description = new_programme.description
        programme.image = new_programme.image

        session.commit()

        return programme
