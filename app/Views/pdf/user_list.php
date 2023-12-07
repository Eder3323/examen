<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-left: 40px;
            padding-right: 40px;
            background-color: #f8f9fa;
        }
        .text-primary {
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .tr_strip {
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .my_td_padding{
            text-align: center;
            padding: 20px;
        }
        .my_td_title{
            text-align: center;
        }
        .my_td {
            text-align: center;
            padding: 10px;
        }
        .my_td_text {
            text-align: left;
            padding: 10px;
        }
        .img-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
        }
        .img-fluid {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
<h1 class="my_td_title">User List</h1>
<table>
    <thead>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr class="tr_strip">
            <td class="my_td_padding"></td>
            <td class="my_td">
                <div class="img-container">
                    <img src="<?= "data:image/png;base64," . base64_encode(file_get_contents(FCPATH.$user['picture'])); ?>"
                         class="img-fluid" alt="User Photo">
                </div>
            </td>
            <td class="my_td_text">
                <?= $user['name'].' '.$user['last_name']; ?> <br>
                <?= $user['phone']; ?><br>
                <div class="text-primary">
                    <?= $user['email']; ?>
                </div>
                <?= $user['id_user_type']; ?>
            </td>
            <td class="my_td_padding"></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
