from sqlalchemy import Column, Integer, String, DateTime, Text
from sqlalchemy.orm import relationship
from datetime import datetime

from .base import Base


class Programme(Base):
    __tablename__ = 'programmes'

    id = Column('id', Integer, primary_key=True)
    programme_id = Column('programme_id', String(8), unique=True, nullable=False)
    title = Column('title', String(255))
    description = Column('description', Text)
    image = Column('image', String(2083))
    created_at = Column('created_at', DateTime, default=datetime.utcnow())
    updated_at = Column('updated_at', DateTime, default=datetime.utcnow(), onupdate=datetime.utcnow())

    entries = relationship("Entry", order_by="Entry.id", backref="programme")

    def __repr__(self):
        return "<Programme(programme_id='%s', title='%s')>" % (self.programme_id, self.title)