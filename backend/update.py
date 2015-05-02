from bbcpodcast.main import fetch_new
import logging

logging.basicConfig(level=logging.INFO)
logging.getLogger("requests.packages.urllib3.connectionpool").setLevel(level=logging.WARN)

fetch_new()