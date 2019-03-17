<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />

    </head>
    <body>
        <div class="container">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
            <div class="row">
                <div class="col-6">
                    <form method="POST" action="/">
                        @csrf
                        <fieldset>
                            <legend>Place a reservation at Sundown Blvd</legend>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                @if(isset($from_error) && !$from_error)
                                    <input type="email" class="form-control" id="email" placeholder="Enter email"
                                name="email" value="{{ $email }}">
                                @else
                                    <input type="email" class="form-control" id="email" placeholder="Enter email"
                                name="email">
                                @endif
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                            </div>
                            <div class="form-group">
                                <label for="number_of_guests">Number of guests</label>
                                @if(isset($from_error) && !$from_error)
                                    <input type="number" class="form-control" id="number_of_guests" placeholder="From 2 to 10" name="number_of_guests" value="{{ $number_of_guests }}">
                                @else
                                    <input type="number" class="form-control" id="number_of_guests" placeholder="From 2 to 10" name="number_of_guests">
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="booked_from">Reserve at</label>
                                @if(isset($from_error) && !$from_error)
                                    <input type="text" class="form-control" id="booked_from" placeholder="e.g. 2019-03-03 18:00:00" name="booked_from" value="{{ $booked_from }}">
                                @else
                                    <input type="text" class="form-control" id="booked_from" placeholder="e.g. 2019-03-03 18:00:00" name="booked_from">
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="btn btn-secondary" onclick="getMeal()">Get the random meal!</div><br>
                            </div>
                            <div class="form-group">
                                <label for="mealName">Meal name</label>
                                <input type="text" readonly="" class="form-control-plaintext" id="mealName" value="-" name="meal_name">
                            </div>
                            <div class="form-group">
                                <label for="mealType">Meal type</label>
                                <input type="text" readonly="" class="form-control-plaintext" id="mealType" value="-" name ="meal_type">
                            </div>
                            <div class="form-group">
                                <label for="drinks">Pick a drink</label>
                                    <select class="form-control" id="drinks" name="drink_id">
                                    </select>
                                    <small id="drinkHelp" class="form-text text-muted">Carefully picked in relation to the meal type.</small>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="the_meal_db_id" value="" id="theMealDbId">
                                <button type="submit" class="btn btn-primary">Place my reservation!</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="col-6">
                    @if(count($errors))
                        <ul class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(isset($from_error) && $from_error)
                    <form method="POST" action="/get_next_available">
                        @csrf
                        <fieldset>
                            <legend>Let Sundown Blvd find an open slot</legend>
                            <div class="form-group">
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="number_of_guests" value="{{ $number_of_guests }}">
                                <input type="hidden" name="booked_from" value="{{ $booked_from }}">
                                <button type="submit" class="btn btn-primary">Get next available reservation</button>
                            </div>
                        </fieldset>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <script>
            function getMeal() {
                let url = '{{ route('meals') }}';
                fetch(url)
                .then(res => res.json())
                .then((out) => {
                    document.getElementById("mealName").value = out.name;
                    document.getElementById("mealType").value = out.type;
                    document.getElementById("theMealDbId").value = out.id;

                    getDrink(out.name, out.type);
                }).catch(err => { throw err });
            }

            function getDrink(name, type) {
                let url = '{{ route('drinks') }}' + '/' + name + '@' + type;
                fetch(url)
                .then(res => res.json())
                .then((out) => {
                    console.log('Checkout this JSON! ', out);
                    drinkMessage = '';
                    if (out.first_result_empty) {
                        drinkMessage = 'Picked among the most popular customer picks.';
                    } else {
                        drinkMessage = 'Carefully picked in relation to the meal type.';               
                    }

                    document.getElementById('drinkHelp').innerHTML = drinkMessage;
                    results = out.results
                    $innerHTML = '';
                    results.forEach(function(entry) {
                        $innerHTML += '<option value="' + entry.id + '">' + entry.name + '</option>';
                    });
                    document.getElementById("drinks").innerHTML = $innerHTML;                      
                }).catch(err => { throw err });
            }
        </script>

    </body>
</html>
