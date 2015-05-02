from sqlalchemy import Column, Integer, String, DateTime, Text, ForeignKey, Boolean
from datetime import datetime

from .base import Base


class Entry(Base):
    __tablename__ = 'entries'

    id = Column('id', Integer, primary_key=True)
    programme_id = Column('programme_id', None, ForeignKey('programmes.id'), nullable=False)
    entry_id = Column('entry_id', String(8), unique=True, nullable=False)
    mediator_id = Column('mediator_id', String(8), unique=True, nullable=False)
    title = Column('title', String(255), nullable=False)
    subtitle = Column('subtitle', Text)
    description = Column('description', Text)
    duration = Column('duration', Integer, nullable=False)
    status = Column('status', Integer, nullable=False)
    image = Column('image', String(2083))
    broadcast_at = Column('broadcast_at', DateTime)
    service = Column('service', String(255))
    bitrate = Column('bitrate', Integer)
    size = Column('size', Integer)
    url = Column('url', String(255))
    created_at = Column('created_at', DateTime, default=datetime.utcnow())
    updated_at = Column('updated_at', DateTime, default=datetime.utcnow(), onupdate=datetime.utcnow())

    def __repr__(self):
        return "<Entry(entry_id='%s', programme_id='%s', title='%s')>" % (self.entry_id, self.programme_id, self.title)