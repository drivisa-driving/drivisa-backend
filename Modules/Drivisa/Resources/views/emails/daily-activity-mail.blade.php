<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Daily Activity Mail to Admin</title>


    <style type="text/css">
        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6;
        }

        body {
            background-color: #f6f6f6;
        }

        @media only screen and (max-width: 640px) {
            h1 {
                font-weight: 600 !important;
                margin: 20px 0 5px !important;
            }

            h2 {
                font-weight: 600 !important;
                margin: 20px 0 5px !important;
            }

            h3 {
                font-weight: 600 !important;
                margin: 20px 0 5px !important;
            }

            h4 {
                font-weight: 600 !important;
                margin: 20px 0 5px !important;
            }

            h1 {
                font-size: 22px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            h3 {
                font-size: 16px !important;
            }

            .container {
                width: 100% !important;
            }

            .content {
                padding: 10px !important;
            }

            .content-wrapper {
                padding: 10px !important;
            }

            .invoice {
                width: 100% !important;
            }
        }

        .lessons {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            margin: 20px auto !important;
        }

        .lessons tr:nth-child(even) {
            background: #f1f7f8;
        }

        .lessons th,
        .lessons td {
            border: 1px solid #a0c8cf;
            padding: .75rem;
            text-align: center;
        }

        .lessons th {
            background: #74afb9;
            color: #fff;
        }

        .lessons td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6; background: #f6f6f6; margin: 0; padding: 0;">

    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; margin:20px auto;" width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px; background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="height:20px;">&nbsp;</td>
                                </tr>
                                <!-- <tr>
                                    <td style="text-align:center;">
                                        <a href="{{config('app.url')}}" title="logo" target="_blank">
                                            <img width="100" src="{{config('app.url')}}/assets/media/logos/drivisa-logo200_80.svg" title="logo" alt="logo" />
                                        </a>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h2 style="color:#1e1e2d; font-weight:500; margin:0;font-family:'Rubik',sans-serif;font-size:22px">
                                            Today's BDE Lesson Activity on Drivisa
                                        </h2>
                                        <span style="color:#1e1e2d; font-weight:500;font-family:'Rubik',sans-serif;font-size:18px;font-weight:500;">( {{ \Carbon\Carbon::now()->format('D, F d, Y') }} )</span>
                                        <div>
                                            <table style="width:100%;padding:5px; margin:20px auto 10px auto; border: 2px solid #74afb9; background-color: white;color:black;font-size:18px">
                                                <tr>
                                                    <th>Total Lessons: {{ $lessons_count }}</th>
                                                </tr>
                                            </table>
                                        </div>

                                        @foreach(json_decode(json_encode($data), true) as $lesson)
                                        <table class="lessons">
                                            <tr>
                                                <th style="width: 150px;">Lesson No.</th>
                                                <td>{{$lesson['no']}}</td>
                                            </tr>
                                            @if($lesson['lesson_type'] == 'Bde')
                                            <tr>
                                                <th>Type</th>
                                                <td>BDE</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <th>Type</th>
                                                <td>{{$lesson['lesson_type']}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Status</th>
                                                <td>{{$lesson['status_text']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Duration</th>
                                                <td>{{$lesson['duration']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Start At</th>
                                                <td>{{$lesson['startAt_formatted']}}</td>
                                            </tr>
                                            <tr>
                                                <th>End At</th>
                                                <td>{{$lesson['endAt_formatted']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Pick Point</th>
                                                <td>{{$lesson['pickupPoint']['address']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Drop Point</th>
                                                <td>{{$lesson['dropoffPoint']['address']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Trainee Name</th>
                                                <td>
                                                    <a href="{{config('app.url')}}/admin/trainees-details/{{$lesson['trainee_id']}}" style="text-decoration:none !important;font-weight:700;text-transform:uppercase; font-size:14px;">{{$lesson['trainee']['fullName']}}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Instructor Name</th>
                                                <td>
                                                    <a href="{{config('app.url')}}/admin/instructors/details/{{$lesson['instructor_id']}}" style="text-decoration:none !important;font-weight:700;text-transform:uppercase; font-size:14px;">{{$lesson['instructor_details']['first_name']}} {{$lesson['instructor_details']['last_name']}}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Cost</th>
                                                <td>${{$lesson['cost']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Tax</th>
                                                <td>${{$lesson['tax']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Additional Km</th>
                                                <td>${{$lesson['additionalCost']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Additional Tax</th>
                                                <td>${{$lesson['additionalTax']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchase Amount</th>
                                                <td>${{$lesson['purchase_amount']}}</td>
                                            </tr>
                                            <tr>
                                                <th>Instructor Lesson Cost</th>
                                                <td>${{$lesson['instructorLessonCost']}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <a href="{{config('app.url')}}/admin/bde-course/bde-log/{{$lesson['trainee']['username']}}" style="text-decoration:none !important;font-weight:700;text-transform:capitalize; font-size:14px;">View Trainee's Course
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">
                                        <a href="{{config('app.url')}}" style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px;text-decoration:none;">
                                            &copy; <strong>Drivisa</strong></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:10px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>