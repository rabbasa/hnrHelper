upload diletakkan di D:/UPLOAD

script ada di C:/htdocs/app1/backend/web

url http://localhost/app1

url file gambar : http://localhost/app1/media/img/9/200/400/1/namafile.jpg

url file pdf : http://localhost/app1/media/doc/19/namadoc.pdf
untuk kasus pdf, dibuat controller :
- nama kontroller : MediaController
- nanti ada action : actionDoc($id,$name) yang menerima parameter id : 19, nama file di database : namadoc.pdf
  tujuan dari namadoc.pdf adalah untuk memvalidasi, apakah record dengan id 19 nama nya sama dengan namadoc.pdf
  agar tidak ada orang yang mengarang2 menginput id 20, 21, dst 


contoh url Mapper :

            'rules' => array(
                    'media/img/<id:\d+>/<w>/<h>/<crop>/<nama>'  => 'media/img',
                    'media/doc/<id:\d+>/<nama>'                 => 'media/doc',
                    '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>/<name>' => '<controller>/<action>',
                    '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>'         => '<controller>/<action>',
                    '<controller:[\w\-]+>/<action:[\w\-]+>'                  => '<controller>/<action>',


http://localhost/app1/media/doc?id=19&name=namadoc.pdf

$get=\yii::$app->request->get();
$get['id']
$get['name']

File-file yang diletakkan di file system sebaiknya direname dengan menghilangkan spasi, koma, kurung, dsb, diganti dengan _ atau -


