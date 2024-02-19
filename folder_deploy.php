<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_POST["ip"];
    $username = $_POST["username"];
    $domain = $_POST["domain"];

    // Upload PEM key
    $targetDir = "/home/sai/"; // Change this path accordingly
    $pemFileName = basename($_FILES["pem"]["name"]);
    $targetFilePath = $targetDir . $pemFileName;

    move_uploaded_file($_FILES["pem"]["tmp_name"], $targetFilePath);

    // Set restrictive permissions on the private key file
    $chmodCommand = "chmod 600 $targetFilePath";
    shell_exec($chmodCommand);

    // Generate public key file
    $pubKeyFile = $targetDir . "$pemFileName.pub";
    $pubKeyCommand = "ssh-keygen -y -f $targetFilePath";
    $pubKeyOutput = shell_exec($pubKeyCommand);

    if ($pubKeyOutput === null) {
        echo "Error generating public key: $pubKeyCommand\n";
        exit;
    }


    file_put_contents($pubKeyFile, $pubKeyOutput);

    // Create domain folder on the remote server
    $domainFolder = "/data/vhosts/$domain/httpdocs";
    $tempCommandFile = "/home/sai/domain_command.txt";
    file_put_contents($tempCommandFile, "mkdir -p $domainFolder");

    $sshCommand = "ssh -o StrictHostKeyChecking=no -i $targetFilePath $username@$ip 'sudo -i /bin/bash -s' < $tempCommandFile 2>&1";
    exec($sshCommand, $output, $returnVar);

    if ($returnVar === 0) {
        echo "Domain folder created successfully on $ip. <br>";

    } else {
        echo "Failed to create domain folder on $ip. <br>";
        echo "SSH Command: $sshCommand <br>";
        echo "SSH Output: <pre>" . implode("\n", $output) . "</pre> <br>";
        exit;
    }


     // Create domain configuration file on the remote server
    $confFile = "/etc/httpd/conf.d/$domain.conf";
    $tempCommandFile2 = "/home/sai/conf_command.txt";
    $confContent = "<VirtualHost *:8080>\n";
    $confContent .= "DocumentRoot \"/data/vhosts/$domain/httpdocs\"\n";
    $confContent .= "ServerName $domain\n";
    $confContent .= "ErrorLog /data/logs/apache/$domain.error_log\n";
    $confContent .= "</VirtualHost>\n";
    file_put_contents($tempCommandFile2, "echo '$confContent' | sudo tee $confFile");

    $sshCommand2 = "ssh -o StrictHostKeyChecking=no -i $targetFilePath $username@$ip 'sudo -i /bin/bash -s' < $tempCommandFile2 2>&1";
    exec($sshCommand2, $output2, $returnVar2);

    if ($returnVar2 === 0) {
        echo "Conf file created successfully on $ip. <br>";
    } else {
        echo "Failed to create Conf file on $ip. <br>";
        echo "SSH Command: $sshCommand2 <br>";
        echo "SSH Output: <pre>" . implode("\n", $output2) . "</pre> <br>";
        exit;
    }

    // Cleanup temporary command files
    unlink($tempCommandFile);
    unlink($tempCommandFile2);
} else {
    echo "Invalid request!";
}
?>
