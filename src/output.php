<html>
    <head>
        <title> WordPress Child Plugin Tool </title>
    <head>
    <body>
        <h1> Modify Plugin Tool </h1>
        <div> <b>Operation: </b> <?php echo $output->operation; ?> </div>
        <div> <b>Parent plugin path: </b> <?php echo remove_server_path($parentPluginDir); ?></div>
        <div> <b>Child plugin path: </b> <?php echo remove_server_path($childPluginDir); ?></div>

        <br />
        <h2> Modified Files </h2>
        <table>
            <tr>
                <th>File</th>
                <th>Status</th>
            </tr>
            <?php foreach($output->modfiedItems as $item){ ?>
                <tr>
                    <td> <?php echo $item->get_shortpath(); ?> </td>
                    <td> <?php echo $item->msg; ?> </td>
                </tr>
            <?php } ?>
        </table>

        <br />
        <h2> Original Files </h2>
        <table>
            <tr>
                <th>File</th>
                <th>Status</th>
            </tr>
            <?php foreach($output->originalItems as $item){ ?>
                <tr>
                    <td> <?php echo $item->get_shortpath(); ?> </td>
                    <td> <?php echo $item->msg; ?> </td>
                </tr>
            <?php } ?>
        </table>
    </body>
</html>