<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Push Notification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>
<body>
@if(Session::has('Notification_Success'))
    <div class="alert alert-success alert-dismissible text-center">
        <a href="/" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {!! Session::get('Notification_Success') !!}
    </div>
@endif
<div class="container vh-100">
    <div class="align-content-center d-flex row vh-100">
        <div class="col-md-6 offset-md-3">
        <div class="card">
    <div class="card-header bg-success">
    <h3 class="font-weight-bold text-center text-white">Broadcast Push Notification</h3>
    </div>
    <div class="card-body">
    <form action="{{url('/clients')}}" method='post'> 
        @csrf
        <input class="form-control" name="title" placeholder="Notification Title" type="text" required>
        <br>
        <textarea class="form-control" rows="5" name="message" placeholder="Notification Body" type="text" required></textarea>
        <br>
    </div>
    <div class="card-footer text-center">
    <button class="btn btn-success rounded-pill px-5" type='submit'>Send Notification</button>
        </form>
    </div>
</div>
        </div>
    </div>
</div>



    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
</body>
</html>