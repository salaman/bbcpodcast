from sqlalchemy import create_engine
from sqlalchemy.orm import scoped_session, sessionmaker

from .config import config

from .models.entry import Entry

engine = create_engine(config.get("db", "url"), pool_recycle=3600)
Session = scoped_session(sessionmaker(bind=engine))

Entry.metadata.create_all(engine)

# def _seed(self):
#     engine.execute(self.programmes.insert(), [
#         {'programme_id': 'b00cjvxv', 'title': 'Crissy Criss'},
#         {'programme_id': 'b01lsv7b', 'title': 'B.Traits'},
#     ])
