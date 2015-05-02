from __future__ import absolute_import
from celery import Celery

from .config import config

app = Celery('bbcpodcast',
             broker=config.get("celery", "broker"),
             backend=config.get("celery", "backend"),
             include=['bbcpodcast.tasks'])

# Optional configuration, see the application user guide.
app.conf.update(
    CELERY_TASK_RESULT_EXPIRES=3600,
    CELERYD_POOL_RESTARTS=True,
    CELERY_IGNORE_RESULT=False,
    CELERY_TASK_SERIALIZER='json',
    CELERY_RESULT_SERIALIZER='json',
    CELERY_ACCEPT_CONTENT=['json'],
    CELERY_TIMEZONE='America/Montreal',
    CELERY_ENABLE_UTC=True,
    CELERY_RESULT_BACKEND=config.get("celery", "backend"),
)

if __name__ == '__main__':
    app.start()
