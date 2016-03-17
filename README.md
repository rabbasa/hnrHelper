
HNR Helper
==========

File Uploader

Tujuannya adalah untuk menyimpan file uploadan di luar folder web di Yii, kemudian orang dapat mengaksesnya melalui url dengan memasukkan id dan nama file yang di-request

informasi dari file yang diupload disimpan di dalam database dengan informasi :
- id
- filename
- filename_real
- size
- content_type
- created_at
- updated_at
- id tambahan untuk referensi misalnya : id_publikasi, id_member, id_blog, dsb

file migrasi untuk create tabel:


        $this->createTable('t_media_file', [
            'id'            => $this->primaryKey(),
            'id_member'     => $this->integer()->notNull(),
            'album_type'    => $this->smallInteger(1), //PROFIL,OTHER
            'is_main'       => $this->smallInteger(1)->defaultValue(0),
            'filename'      => $this->string(230), // image or cover photo
            'filename_real' => $this->string(250),
            'size'          => $this->integer()->defaultValue(0),
            'content_type'  => $this->string(75),
            'created_at'    => 'integer' ,
            'updated_at'    => 'integer' ,
        ]);


misal upload diletakkan di D:/UPLOAD (kalau windows), atau _FILES di luar folder web di Yii

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


