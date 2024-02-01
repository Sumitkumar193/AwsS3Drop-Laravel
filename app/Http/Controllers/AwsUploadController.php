<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AwsUploadController extends Controller
{
    protected $client;

    public function __construct()
    {
        $config = [
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'signature_version' => 'v4',
        ];
        $this->client = new S3Client($config);
    }

    /**
     * @method get_pre_post_signed_url()
     * @description Get pre signed url for upload file to s3 bucket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_pre_post_signed_url(Request $request)
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $formInputs = ['acl' => 'public-read'];
        $expires = '+5 minutes';
        $fileKeys = $request->input('fileKeys');
        $urls = [];
        try {
            foreach($fileKeys as $file => $length) {
                $name = strtolower(pathinfo($file, PATHINFO_FILENAME));
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $file_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $name) . '-' . uniqid() . '.' . $extension;

                $options = [
                    ['acl' => 'public-read'],
                    ['bucket' => $bucket],
                    ['starts-with', '$key', "offline-works/$file_name"],
                ];
                $postObject = new \Aws\S3\PostObjectV4(
                    $this->client,
                    $bucket,
                    $formInputs,
                    $options,
                    $expires
                );
    
                $formAttributes = $postObject->getFormAttributes();
                $formInputs = $postObject->getFormInputs();

                $urls[$file] = [
                    'formAttribute' => $formAttributes,
                    'formInputs' => $formInputs,
                    'file_name' => $file_name,
                ];
            }

            $response = [
                'status' => 'success',
                'data' => $urls,
            ];

            return response()->json($response, 200);
        } catch (AwsException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}