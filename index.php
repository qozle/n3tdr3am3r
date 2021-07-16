<?PHP

$img_num = rand(1,5);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>n3t dr34m3r</title>

        <link ref='css/bootstrap.min.css' rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script src='js/jquery-3.6.0.min.js'></script>
        <script src="js/bootstrap.min.js"></script>
        <style>
            body {
                background-color: #0b0b03;
            }

            #content {
                background-color: #0b0b03;
            }

            #main-header {
                color: darkgreen;
                display: block;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 10px;
            }

            #description {
                color: snow;
                display: block;
                text-align: center;
            }

            #header {
                display: block;
                margin-bottom: 50px;
            }

            #main-table {
                min-height: 300px;
                margin-left: auto;
                margin-right: auto;
                height: auto;
                background-color: #0e0e0b;
                color: white; 
                width: 80%;
            }

            #dream-text {
                padding: 10px;
                text-align: center;
                color: white;
                font-size: 1.1em;
                background-color: #0e0e0b;
            }

            #loading-gif {
                display: block;
                margin-left: auto;
                margin-right: auto;
            }

            #the-button {
                display: block;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 10px;
                margin-top: 10px;
            }

            #loading-text {
                text-align: center;
            }

            #error-text {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class='container-fluid' id='content'>
            <div class='row'>
                <div class='col'>
                    <div id='header'>
                        <h1 id='main-header'>n3t dr34m3r</h1>
                        <small id='description'><i>Thoughtful prose and schizo dream-text generated from the subconscious of the interwebs.</i></small>
                    </div>

                    <div id='main-table'>
                    </div>

                    <div>
                        <button id='the-button' type='button' class='btn'>Generate Dream</button>
                    </div>
                </div>
            </div>

        </div>

        <script>
            $(document).ready(()=>{

                $('#the-button').click(()=>{
                    $('#main-table').empty();
                    $('#main-table').append("<img id='loading-gif' src='img/loading<?PHP echo rand(1,5); ?>.gif'>");
                    $('#main-table').append("<p id='loading-text'>loading...</p>");
                    $.ajax({
                        url: 'n3tdr34m3r.php',
                        method: 'GET', 
                        statusCode: {
                            200: (resp)=>{
                                console.log(resp);
                                $('#loading-gif').remove();
                                $('#loading-text').remove();
                                $('#main-table').append(`<p id='dream-text'>${resp}</p>`);
                            },
                            500: (error)=>{
                                $('#loading-gif').remove();
                                $('#loading-text').remove();
                                $('#main-table').append("<p id='error-text'>Ooops, there was an error...reload and try again...<p>");
                            }
                        }
                    });
                })

            });
        </script>
    </body>

</html>