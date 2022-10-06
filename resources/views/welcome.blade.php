<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
            crossorigin="anonymous"></script>
</head>
<body class="container">
<div class="row mt-3">
    <form id="formData">
        @csrf
        <div class="input-group">
            <select required class="form-control"
                    id="site" name="site">
                <option value="udemy">Udemy</option>
                <option value="eduonix">Eduonix</option>
            </select>
        </div>
        <br>
        <div class="input-group">
            <input required type="text" class="form-control"
                   id="exampleInputCourseLink"
                   placeholder="{{__('Enter Course Link')}}" name="courseLink">
            <input type="submit" id="scrap" class="btn btn-success" value="Scrap It">
        </div>
    </form>
    <div class="auto-load text-center my-3" style="display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only"></span>
        </div>
    </div>
    <hr>
    <hr>
    <div class="d-flex justify-content-between">
        <h4>Course Name</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('courseName')">Copy</button>
    </div>
    <div id="courseName"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Unique Name</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('uniqueName')">Copy</button>
    </div>
    <div id="uniqueName"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Description in Arabic</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('descriptionAr')">Copy</button>
    </div>
    <div id="descriptionAr"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Description in English</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('descriptionEn')">Copy</button>
    </div>
    <div id="descriptionEn"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Image Link</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('imgLink')">Copy</button>
    </div>
    <div id="imgLink"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Course Link</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('courseLink')">Copy</button>
    </div>
    <div id="courseLink"></div>
    <hr>

    <div class="d-flex justify-content-between">
        <h4>Category</h4>
        <button class="btn btn-sm btn-primary" onclick="CopyToClipboard('category')">Copy</button>
    </div>
    <div id="category"></div>
</div>
<script type="text/javascript">
    $('#scrap').click(function (e) {
        e.preventDefault();
        // let formData = new FormData($('#formData')[0]);
        let site = $('#site').val();
        let requestLink = '';
        if (site === 'udemy') {
            requestLink = "{{route('scrape.udemy')}}";
        } else if (site === 'eduonix') {
            requestLink = "{{route('scrape.eduonix')}}";
        } else {
            alert('Please Select Site');
            return false;
        }
        var url = $('#exampleInputCourseLink').val()
        console.log(url)
        $.ajax({
            type: 'get',
            url: requestLink,
            data: {
                url: url
            },
            beforeSend: function () {
                $('.auto-load').show();
            },
            success: function (data) {
                $('.auto-load').hide();
                console.log(data);
                $('#courseName').html(data.name);
                $('#uniqueName').html(data.uniqueName);
                $('#descriptionAr').html(data.description_ar);
                $('#descriptionEn').html(data.description_en);
                $('#imgLink').html(data.imgLink);
                $('#courseLink').html(data.courseLink);
                $('#category').html(data.category);
                // $('#coming_from_ajax').html(data);
            },
            error: function (reject) {
                let a_errors = reject.responseJSON.errors;
                console.log(a_errors);
            },
        });
    });

    function CopyToClipboard(containerid) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
        alert('Copied');
    }
</script>
</body>
</html>
