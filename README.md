# wp-ra-komando-bridge_remastered
Este plugin añade la función de BasicAuth via api get o post (User and password and headers estan habilitadas para el envio).

Ejemplo en Python para get post data:
    passw=data_passw
    user=data_user
    headers = {'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36'}
    pages = r.get(domain+'wp-json/wp/v2/posts/id', auth=(user, passw), headers=headers)
    data = json.loads(pages.content.decode('utf-8'))
    print(data)
    
Ejemplo para los custom fields:

#seo_meta_tags
    pages = r.post(domain+'wp-json/wp/v2/pages/id',
    data={'seo_meta_tag':'odio php desde las pages'},
    auth=(user, passw), headers=headers)

#seo_schema
    pages = r.post(domain+'wp-json/wp/v2/pages/id',
    data={'seo_schema':'odio php desde las pages'},
    auth=(user, passw), headers=headers)

Estos ejemplos son validos para enviar cualquier tipo de informacion informacion a los custom fields, el funcionamiento real para los schemas o meta tags viene dada por como en su lenguaje les permita enviar la informacion en un string
y recibirla en la funcion de php json_decode().
