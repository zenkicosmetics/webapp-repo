<h1 style="font-size: 18pt;">List all deleted customers which has open balance > 0</h1>

<table border="1">
    <thead>
        <th>Customer code</th>
        <th>Email</th>
        <th>Open balance</th>
        <th>Status</th>
    </thead>
    <tbody>
        <?php foreach($result as $r):?>
            <tr>
                <td><?php echo $r->customer_code;?></td>
                <td><?php echo $r->email;?></td>
                <td><?php echo $r->open_balance;?></td>
                <td><?php echo $r->status == 1 ? "deleted" : "";?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>

