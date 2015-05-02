from .ripper import Ripper
from .db import Session
from .models.programme import Programme
from .tasks import download_episode

from requests import HTTPError


def fetch_new():
    session = Session()
    ripper = Ripper(session)

    programmes = session.query(Programme)

    episodes = []

    for programme in programmes:
        try:
            episodes += ripper.fetch_new_episodes_for_programme(programme)
        except HTTPError:
            pass

    for episode in episodes:
        download_episode.delay(episode.id)

    session.close()
    Session.remove()

    return episodes