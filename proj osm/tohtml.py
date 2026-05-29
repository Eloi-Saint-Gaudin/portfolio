import markdown

def tohtml(): 
    with open("sae15.md", "r", encoding="utf-8") as f: 
        md_text = f.read()
    html = markdown.markdown(md_text)
    with open("sae15.html", "w+", encoding="utf-8") as f : 
        f.write(html)