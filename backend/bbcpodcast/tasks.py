from __future__ import absolute_import

from .celery import app
from .downloader import Downloader
from .ripper import Ripper
from .db import Session
from .models.programme import Programme
from .models.entry import Entry


class DatabaseTask(app.Task):
    """An abstract Celery Task that ensures that the connection the the
    database is closed on task completion"""
    abstract = True

    def after_return(self, *args, **kwargs):
        Session.remove()


@app.task(bind=True, base=DatabaseTask, max_retries=3)
def download_episode(self, entry_id):
    session = Session()
    downloader = Downloader(session)

    # Grab Entry from database
    entry = session.query(Entry).filter(Entry.id == entry_id).one()

    try:
        downloader.download_episode(entry)
    except Exception as exc:
        raise self.retry(exc=exc, countdown=60 * 30 * self.request.retries)

    session.close()

    return True


@app.task(base=DatabaseTask)
def update_programme_metadata(programme_id):
    session = Session()
    ripper = Ripper(session)

    # Get Programme from database by id
    programme = session.query(Programme).filter(Programme.id == programme_id).one()

    ripper.update_programme_metadata(programme)

    session.close()

    return True


@app.task(base=DatabaseTask)
def update_programme(programme_id):
    session = Session()
    ripper = Ripper(session)

    # Get Programme from database by id
    programme = session.query(Programme).filter(Programme.id == programme_id).one()

    episodes = ripper.fetch_new_episodes_for_programme(programme)

    for episode in episodes:
        download_episode.delay(episode.id)

    session.close()

    return True
