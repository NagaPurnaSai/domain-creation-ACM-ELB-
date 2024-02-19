<?php

$config = require 'config.php';
require 'vendor/autoload.php'; // Include AWS SDK for PHP

use Aws\Credentials\Credentials;
use Aws\Acm\AcmClient;
use Aws\ElasticLoadBalancingV2\ElasticLoadBalancingV2Client;
// Load AWS credentials, ELB ARN, Target Group ARN, and Priority from config file

$awsCredentials = $config['aws'];
$elbConfig = $config['elb'];

$credentials = new Credentials($awsCredentials['key'], $awsCredentials['secret']);

// Initialize ACM and ELB clients
$acmClient = new AcmClient([
    'version' => 'latest',
    'region' => $awsCredentials['region'],
    'credentials' => $credentials
]);

$elbClient = new ElasticLoadBalancingV2Client([
    'version' => 'latest',
    'region' => $awsCredentials['region'],
    'credentials' => $credentials
]);
// Get domain from form input
$domain = $_POST['domain'];

/*


// Step 1: Check if the certificate already exists
try {
    $existingCertificates = $acmClient->listCertificates();
    foreach ($existingCertificates['CertificateSummaryList'] as $certificate) {
        if ($certificate['DomainName'] === $domain) {
            // Certificate already exists, no need to request a new one
            $certificateArn = $certificate['CertificateArn'];
            echo "Certificate already exists for $domain. CertificateArn: $certificateArn <br>";
            break;
        }
    }

    // If the certificate does not exist, request a new one
    if (!isset($certificateArn)) {
        $result = $acmClient->requestCertificate([
            'DomainName' => $domain,
            'ValidationMethod' => 'DNS', // Change if necessary
        ]);
        $certificateArn = $result['CertificateArn'];
        echo "Certificate requested successfully for $domain. CertificateArn: $certificateArn <br>";
    }
} catch (Exception $e) {
    echo "Failed to request/check certificate: " . $e->getMessage() . "<br>";
}

// Step 2: Retrieve CNAME value from ACM if the certificate exists
if (isset($certificateArn)) {
    try {
        $startTime = time();
        $timeout = 10; // in seconds

        while (time() - $startTime < $timeout) {
            $certificate = $acmClient->describeCertificate([
                'CertificateArn' => $certificateArn,
            ]);

            if (isset($certificate['Certificate']['DomainValidationOptions']) && !empty($certificate['Certificate']['DomainValidationOptions'])) {
                $validationOptions = $certificate['Certificate']['DomainValidationOptions'];

                foreach ($validationOptions as $option) {
                    if (isset($option['ResourceRecord']) && $option['ResourceRecord']['Type'] === 'CNAME') {
                        $cname = $option['ResourceRecord']['Name'];
                        $cvalue = $option['ResourceRecord']['Value'];
                        echo "CNAME: $cname <br>";
                        echo "CVALUE: $cvalue <br>";
                        exit; // Exit the script
                    }
                }
            }

            // Sleep for a short interval before checking again
            usleep(500000); // 500 milliseconds
        }

        echo "Timed out while waiting for CNAME value. Please refresh the page after some time. <br>";
    } catch (Exception $e) {
        echo "Failed to retrieve CNAME and CVALUE: " . $e->getMessage() . "<br>";
    }
}

echo "Deployment completed successfully!";

try {
    $elbClient->associateDomain([
        'DomainName' => $domain,
        'DomainValidationOptions' => [
            [
                'DomainName' => $domain,
                'ValidationDomain' => $domain,
            ],
        ],
        'CertificateArn' => $certificateArn,
    ]);
    echo "Domain associated with Elastic Load Balancer successfully. <br>";
} catch (Exception $e) {
    echo "Failed to associate domain with Elastic Load Balancer: " . $e->getMessage() . "<br>";
}

try {
    $elbClient->createRule([
        'ListenerArn' => $elbConfig['listener_arn'],
        'Priority' => $elbConfig['priority'],
        'Conditions' => [
            [
                'Field' => 'host-header',
                'Values' => [
                    $domain,
                ],
            ],
        ],
        'Actions' => [
            [
                    'Type' => 'forward',
                     'TargetGroupArn' => $elbConfig['target_group_arn'],
            ],
        ],
    ]);
    echo "Domain associated with Elastic Load Balancer successfully. <br>";
} catch (Exception $e) {
    echo "Failed to associate domain with Elastic Load Balancer: " . $e->getMessage() . "<br>";
}

// Step 9: Configure HTTPS listener with forwarding rules
try {
    $elbClient->createListener([
        'DefaultActions' => [
            [
                    'Type' => 'forward',
                     'TargetGroupArn' => $elbConfig['target_group_arn'],
            
            ],
        ],
         'LoadBalancerArn' => $elbConfig['lb_arn'],
        'Port' => 443,
        'Protocol' => 'HTTPS',
        'Certificates' => [
            [
                'CertificateArn' => $certificateArn,
            ],
        ],
    ]);
    echo "HTTPS listener configured successfully for the domain. <br>";
} catch (Exception $e) {
    echo "Failed to configure HTTPS listener: " . $e->getMessage() . "<br>";
}
*/





echo '<form action="folder_creation.php" method="get">';
echo '<input type="submit" value="Next">';
echo '</form>';

?>
