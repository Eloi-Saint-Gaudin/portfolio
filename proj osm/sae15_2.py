import markdown
import requests
import math
import argparse
from PIL import Image
import time

parser = argparse.ArgumentParser()
parser.add_argument("filename", help="fichier cible")
parser.add_argument("ville", help="la ville")
args = parser.parse_args()

filename = args.filename
ville = args.ville

def deg2num(lat_deg, lon_deg, zoom = 10):

  lat_rad = math.radians(lat_deg)
  n = 1 << zoom
  xtile = int((lon_deg + 180.0) / 360.0 * n)
  ytile = int((1.0 - math.asinh(math.tan(lat_rad)) / math.pi) / 2.0 * n)
  return int(xtile), int(ytile)

def get_dataset(ville):

    url = f"https://overpass-api.de/api/interpreter"
    query = f"""
    [out:json];
    (
        area[name={ville}][admin_level=8];
    )->.ville;
    nwr[amenity=bar](area.ville);
    out;
    """
    query_latlon = f"""
    [out:json];
    area[name="{ville}"]->.searchArea;
    nwr["place"~"city|town"](area.searchArea);
    out body;
    """
    response = requests.get(url, data={"data" : query})

    while response.status_code==504:
        time.sleep(3)
        response = requests.get(url, data={"data" : query})
    print(response.status_code)
    try: 
        données=response.json()
    except ValueError: 
        données = None

    if données == None: 
        return print("Il n'y a pas de bar dans", ville)
    
    if not données["elements"]: 
        return print("SANS NOM")

    response_latlon = requests.get(url, data={"data" : query_latlon})
    while response_latlon.status_code==504 or response_latlon.status_code==429:
        time.sleep(3)
        response_latlon = requests.get(url, data={"data" : query_latlon})
    print(response_latlon.status_code)
    try: 
        données_latlon=response_latlon.json()
    except ValueError: 
        données_latlon = None
    
    terrasse = 0
    tabac = 0
    les_deux = 0
    total = 0
    for éléments in données["elements"]:
        total+=1
        ter_temp=terrasse
        tabac_temp=tabac
        tags = éléments.get("tags", {})
        for key1, value1 in tags.items():
            if key1 == "outdoor_seating" and value1 == "yes": 
                terrasse+=1
            if key1 == "tobacco" and value1 == "yes": 
                tabac+=1
            if ter_temp!=terrasse and tabac_temp!=tabac:
                les_deux+=1
    gros_dico = {"bar":total, "tabac":tabac, "terrasse":terrasse, "les deux":les_deux, "lat et lon":données_latlon}
    return gros_dico

dataset = get_dataset(ville)

def compute_statistics(): 
    bar = dataset["bar"]
    terrasse =  dataset["terrasse"]
    tabac = dataset["tabac"]
    les_deux = dataset["les deux"]
    stat_tobacco = (int(tabac)/int(bar))*100
    stat_outdoor_seating = (int(terrasse)/int(bar))*100
    stat_both = (int(les_deux)/int(bar))*100
    return stat_both, stat_outdoor_seating, stat_tobacco, bar, terrasse, tabac, les_deux

def dataset_to_md(dataset: dict, filename:str)->None: 
    lat = dataset["lat et lon"]["elements"][0]["lat"]
    lon = dataset["lat et lon"]["elements"][0]["lon"]
    x, y = deg2num(lat, lon)
    stat_both, stat_outdoor_seating, stat_tobacco, count, count_outdoor_seating, count_tobbaco, count_both = compute_statistics()
    md = f"# SAE15\n\n## {ville}\n\n"
    var = f"{md} Il y a {count} de bar à {ville}\n\n ![alt text](https://tile.openstreetmap.org/10/{x-1}/{y-1}.png)![alt text](https://tile.openstreetmap.org/10/{x}/{y-1}.png)![alt text](https://tile.openstreetmap.org/10/{x+1}/{y-1}.png)\n\n ![alt text](https://tile.openstreetmap.org/10/{x-1}/{y}.png)![alt text](https://tile.openstreetmap.org/10/{x}/{y}.png)![alt text](https://tile.openstreetmap.org/10/{x+1}/{y}.png)\n\n![alt text](https://tile.openstreetmap.org/10/{x-1}/{y+1}.png)![alt text](https://tile.openstreetmap.org/10/{x}/{y+1}.png)![alt text](https://tile.openstreetmap.org/10/{x+1}/{y+1}.png)\n\n Il y a {count_tobbaco} de ces bars qui vendent du tabac ce qui fait {int(stat_tobacco)}% des bars à {ville}\n\n Il y a {count_outdoor_seating} de ces bars qui ont une terrasse ce qui fait {int(stat_outdoor_seating)}% des bars à {ville}\n\n Il y a {count_both} de ces bars qui font les 2 ce qui fait {int(stat_both)}% des bars à {ville}"
    with open(f"{filename}.md", "w+", encoding="utf")as f: 
        f.write(var)


def infos_locales(): 
    with open(f"{filename}.md", "r", encoding="utf-8") as f: 
        md_text = f.read()
    html = markdown.markdown(md_text)
    with open(f"{filename}.html", "w+", encoding="utf-8") as f : 
        f.write(html)

dataset_to_md(dataset, filename)
infos_locales()



