<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        thead{
            background: pink;
        }
        caption{
            text-align: center ;
            caption-side: bottom;
        }
        tbody{
            background: yellow  ;
        }
    </style>
</head>
<body>
    <table border="1">
        <caption>All Times Central</caption>
        <thead>
            <tr>
                <th>Time</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Web</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
                <th>Sun</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>6:00 PM</th>
                <td colspan="7">National News</td>
            </tr>
            <tr>
                <th>6:30 PM</th>
                <td colspan="7">World News</td>
            </tr>
            <tr>
                <th>7:00 PM</th>
                <td rowspan="2" style="background:cyan;">Opera Fest</td>
                <td rowspan="2" style="background:cyan;">Radio U</td>
                <td rowspan="2" style="background:cyan;">Science Week</td>
                <td rowspan="2" style="background:cyan;">The Living World</td>
                <td>World Play</td>
                <td>Agri-Week</td>
                <td rowspan="2">Folk Fest</td>
            </tr>
            <tr>
                <th>7:30 PM</th>
                <td>Brain Stew</td>
                <td>Bismarck Forum</td>
            </tr>
        </tbody>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th>Assessment Type</th>
                <th></th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Assignment</td>
                <td>:</td>
                <td>40%</td>
            </tr>
            <tr>
                <td>Test</td>
                <td>:</td>
                <td>50%</td>
            </tr>
            <tr>
                <td>Exercise</td>
                <td>:</td>
                <td>10%</td>
            </tr>
        </tbody>
    </table>
    
    <table border="1">
        <caption>My Employee Details</caption>
        <thead>
            <tr>
                <th colspan="3">ABC Company</th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Telephone</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jack</td>
                <td>Sales</td>
                <td>555-5555</td>
            </tr>
            <tr>
                <td>John</td>
                <td>Admin</td>
                <td>555-55555</td>
            </tr>
            <tr>
                <td>James</td>
                <td>Sales</td>
                <td>555-55555</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total: 3 employees</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>