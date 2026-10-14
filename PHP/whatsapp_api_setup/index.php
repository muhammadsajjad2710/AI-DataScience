<?php
if(isset($_POST['submit'])){
  $mob_no=$_POST['mob_no'];
  $msg=$_POST['msg'];
  $file_name='';
  $media_url='';
  if(isset($_FILES['video']['name'])){
    $video=$_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    move_uploaded_file($video_tmp,"./video/$video");
    $file_name = $video;
    $media_url = "http://logicwebplus.com/whatsapp/".$file_name;

  }
  if(isset($_FILES['video']['name'])){
    $video=$_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    move_uploaded_file($video_tmp,"./video/$video");
    $file_name = $video;
    $media_url = "http://logicwebplus.com/whatsapp/video/".$file_name;

  }
  if(isset($_FILES['pdf']['name'])){
    $pdf=$_FILES['pdf']['name'];
    $pdf_tmp = $_FILES['pdf']['tmp_name'];
    move_uploaded_file($pdf_tmp,"./pdf/$pdf");
    $file_name = $pdf;
    $media_url = "http://logicwebplus.com/whatsapp/pdf/".$file_name;

  }
  if(isset($_FILES['image']['name'])){
    $image=$_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($image_tmp,"./image/$image");
    $file_name = $image;
    $media_url = "http://logicwebplus.com/whatsapp/image/".$file_name;

  }

  $send_whatsapp_msg="https://sendingtool.com/api/send.php?number=".$mob_no."&type=media&message=".$msg."&media_url=".$media_url."&filename=".$file_name."&instance_id=&access_token=";
  file_get_contents($send_whatsapp_msg);  


}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Whatsapp Api Setup</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12" style="padding-top: 50px">
          <img
            src="https://5.imimg.com/data5/SELLER/Default/2024/9/454891065/RJ/YG/QQ/223561495/whatsapp-marketing-service.jpg"
            style="width: 100%; height: auto"
          />
        </div>
        <div class="col" style="padding-top: 50px">
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label"
                >Mobile No.(+920000000000</label
              >
              <input
                type="text"
                class="form-control"
                placeholder="Enter Mobile No."
                name="mob_no"
                aria-describedby="emailHelp"
              />
            </div>
            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label"
                >Message</label
              >
              <input type="text" class="form-control" name="msg" placeholder="Message" />
            </div>

            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label"
                >Image</label
              >
              <input type="file" class="form-control" name="image" />
            </div>

            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label">Pdf</label>
              <input type="file" class="form-control" name="pdf" />
            </div>

            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label"
                >Audio</label
              >
              <input type="file" class="form-control" name="audio" />
            </div>

            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label"
                >Video</label
              >
              <input type="file" class="form-control" name="video" />
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Send Now</button>
          </form>
        </div>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
