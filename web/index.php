<?php 
      require_once("authentication.php");
?>

<html>
    <head>
        <style>
            body{
              background: #2b2b2b;
            }
            form{
              position: absolute;
              top: 50%;
              left: 50%;
              margin-top: -500px;
              margin-left: -250px;
              width: 500px;
            }



            p{
              width: 100%;
              height: 100%;
              text-align: center;
              line-height: 170px;
              color: #ffffff;
              font-family: Arial;
            }

            .spacer {
                margin-bottom: 25px;
                margin-top: 25px;
                color: #c6c6c680;
                font-style: italic;
                text-align: center;
            }

             h3 {
                    margin-bottom: 25px;
                    margin-top: 25px;
                    color: #fff;
                    text-align: center;
                }

            a {
                color: inherit;

            }

            #url input{
               width: 100%;
               height: 40px;
               background-color: #2e2e2e;
               color:#fff;
               border: 2px solid white;
            }


            textarea {
                width: 100%;
                height: 200px;
                background-color: #2e2e2e;
                color:#fff;
                border: 2px solid white;

            }

            textarea:focus::placeholder {
              color: transparent;
            }

            input:focus::placeholder {
              color: transparent;
            }

            ::-webkit-input-placeholder {
                color: #ffffff;
                font-family: Arial;
                line-height:190px;
                text-align: center;
                font-size: large;
            }

            ::-moz-placeholder { /* Mozilla Firefox 19+ */
                color: #ffffff;
                font-family: Arial;
                line-height:190px;
                text-align: center;
                font-size: large;
            }
            ::-webkit-input-placeholder { /* Webkit */
                color: #ffffff;
                font-family: Arial;
                line-height:190px;
                text-align: center;
                font-size: large;
            }
            :-ms-input-placeholder { /* IE */
                color: #ffffff;
                font-family: Arial;
                line-height:190px;
                text-align: center;
                font-size: large;
            }

            #fileArea {
                        background: #2e2e2e;
                width: 100%;
                height: 200px;
                border: 4px dashed #fff;
            }

            #fileArea input{
              position: absolute;
              margin: 0;
              padding: 0;
              width: 100%;
              height: 200px;
              outline: none;
              opacity: 0;
              cursor: pointer;
            }

            .checkboxRow{
                margin-top: 15px;
                color: #fff;
            }

            .checkboxRow .checkbox{
                display: inline;
                margin-left: 10px;
            }

            form button{
              margin: 0;
              color: #fff;
              background: #16a085;
              border: none;
              width: 508px;
              height: 40px;
              font-size: 16px;
              margin-top: 50px;
              margin-left: -4px;
              border-radius: 4px;
              border-bottom: 4px solid #117A60;
              transition: all .2s ease;
              outline: none;
                  cursor: pointer;
            }
            form button:hover{
              background: #149174;
                color: #fffff;
            }
            form button:active{
              border:0;
            }

            .progressBar{
                height: 20px;
                color: #fff;
                background-color: black;
                border-radius: 6px;
            }

            .progressBar-container{
                height:100%;
                color: #fff;
                background-color: gray;
                padding: 0.01em 16px;
               border-radius: 6px;
            }

        </style>
    </head>

    <body>
         <form id="uploadForm" enctype="multipart/form-data" method="POST">
            <div id="step1">
                <h3>Easy share</h3>
                <div id="url">
                    <input name="url" placeholder="Add redirect url" type="url"/>
                </div>

                <div class="spacer">
                       OR
                </div>

                <div id="snippet">
                    <textarea name="snippet" placeholder="Type or paste your snippet here" type="textarea"></textarea>
                    <div class="checkboxRow">
                      <div class="checkbox">
                         <input type="checkbox" name="raw" value="1"/>
                         <label for="metadata">Raw</label>
                      </div>
                    </div>
                </div>

                <div class="spacer">
                       OR
                </div>

                <div id="file">
                   <div id="fileArea">
                    <input name="file" onChange='onFileAdd(this)' type="file">
                    <p id="filePlaceholder">Drag your file here</p>
                   </div>
                   <div class="checkboxRow">
                     <div class="checkbox">
                        <input type="checkbox" checked name="exifdata" value="1"/>
                        <label for="exifdata">Remove EXIF</label>
                     </div>
                     <div class="checkbox">
                        <input type="checkbox" checked name="compressImage" value="1"/>
                        <label for="compressImage">Compress images</label>
                     </div>
                     <div class="checkbox">
                        <input type="checkbox" checked name="randomName" value="1"/>
                        <label for="randomName">Random filename</label>
                      </div>
                   </div>
                 </div>
                <button onClick="uploadForm()" type="button">Upload</button>
            </div>
            <div id="step2">
                <div id="uploadError" style="display: none" >
                    <h3>Error while uploading</h3>
                    <h3 id="responseMessage"></h3>
                </div>
                <div id="uploadSuccess" style="display: none" >
                    <h3>URL</h3>
                    <h3><a href="" id="responseUrl"></a></h3>
                </div>

                <div id="uploading" style="display: none" >
                    <h3>Uploading...</h3>
                     <div class="progressBar">
                      <div id="progress" class="progressBar-container" style="width: 0%"></div>
                     </div>
                </div>

            </div>
         </form>
    </body>

    <footer>
        <script>
            function onFileAdd(event){
                if(event.files.length > 0){
                    files = "Selected file: "
                    for(var file of event.files){
                        files += file.name + " "
                    }
                    document.getElementById("filePlaceholder").innerHTML = files;
                }else{
                    document.getElementById("filePlaceholder").innerHTML = "Drag your file here";
                }
            }

            function uploadForm(){
                var form = document.getElementById('uploadForm');
                var formData = new FormData(form);

                var xhr = new XMLHttpRequest();
                xhr.responseType = 'json';
                xhr.open('POST', 'uploader.php' + window.location.search, true);

                window.history.pushState({}, 'File uploaded', '');
                document.getElementById("step1").style.display = 'none';
                document.getElementById("step2").style.display = 'block';
                document.getElementById("uploading").style.display = 'block';

                xhr.upload.onprogress = function(e) {
                    var percentComplete = Math.ceil((e.loaded / e.total) * 100);
                    document.getElementById("progress").style.width = percentComplete + '%';
                };

                xhr.onload = function() {
                    if(this.status == 200) {
                        document.getElementById("uploading").style.display = 'none';
                        if(this.response["success"]){
                             document.getElementById("uploadSuccess").style.display = 'block';
                             document.getElementById("uploadError").style.display = 'none';
                             document.getElementById("responseUrl").innerHTML = this.response["url"];
                             document.getElementById("responseUrl").href = this.response["url"];
                        }else{
                            document.getElementById("uploadError").style.display = 'block';
                            document.getElementById("uploadSuccess").style.display = 'none';
                            document.getElementById("responseMessage").innerHTML = this.response["error"];
                        }
                    }else{

                    }
                 }

                 xhr.send(formData);
            }

            window.addEventListener('popstate', function(event) {
                // Reset view
                document.getElementById("step1").style.display = 'block';
                document.getElementById("step2").style.display = 'none';
            });
         </script>
    </footer>
</html>
