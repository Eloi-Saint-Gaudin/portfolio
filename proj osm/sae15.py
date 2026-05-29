import requests
import math
from PIL import Image
from tohtml import tohtml

def deg2num(lat_deg, lon_deg, zoom = 17):
  lat_rad = math.radians(lat_deg)
  n = 1 << zoom
  xtile = int((lon_deg + 180.0) / 360.0 * n)
  ytile = int((1.0 - math.asinh(math.tan(lat_rad)) / math.pi) / 2.0 * n)
  return int(xtile), int(ytile)

def get_node(id:int)->dict: 
    url = f"https://www.openstreetmap.org/api/0.6/node/{id}.json"
    response = requests.get(url)
    print(response.status_code)
    try: 
        données=response.json()
    except ValueError: 
        données = None

    if données == None: 
        return print(id, "N'existe pas")
    
    if not données["elements"]: 
        return print("SANS NOM")
    return données

def node_to_md(data: dict, filename: str)->None:
    tags = data["elements"][0].get("tags", {})
    lat = data["elements"][0]["lat"]
    lon = data["elements"][0]["lon"]
    md = "# SAE15\n\n"
    for key, value in tags.items():
        print(f"{key}: {value}")
        md += f"**<u>{key}</u>**: {value}\n\n"
    x, y = deg2num(lat, lon)
    md+= f"![alt text](https://tile.openstreetmap.org/17/{x}/{y}.png)"
    with open(filename, "w+", encoding="utf8") as f: 
        f.write(md)
    return tags

def fiche_osm(id:int)->None:
    get_node(id)
    node_to_md(get_node(id), 'sae15.md')
    tohtml()

fiche_osm(13064882501)