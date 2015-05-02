## BBC Radio Podcast Ripper

A small project to auto-download BBC Radio shows and turn them into an iTunes-compatible podcast feed.

Laravel project (root) is a web PHP front-end. Python ripper backend set up for OpenStack Swift uploads is in `/backend/`.

Create `settings.cfg` from the provided example and set a cronjob to run `update.py`.

Requires Celery, Python 2.7 and PHP >5.4.
