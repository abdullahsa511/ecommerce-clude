The easiest and most secure way to copy a folder between EC2 instances in different AWS accounts is by using Amazon S3 as a bridge, or a direct SCP transfer. Use S3 if the folder is large or the instances cannot communicate directly. Use SCP for quick, small file transfers.Method 1: Using Amazon S3 (Best for Large Data)This method lets your instances act as bridges without exposing them to the open internet.Step 1: Set up the S3 BucketLog into your Destination AWS Account.Open the Amazon S3 Console and create a bucket (e.g., my-shared-folder-bucket).Under the Permissions tab, select Bucket Policy. Add a policy that allows the Source EC2’s IAM Role or Account to upload files.Set Object Ownership to Bucket owner enforced to avoid access permission conflicts.Step 2: Sync from the Source EC2Attach an IAM Role (or configure AWS CLI credentials) to your Source EC2 that permits uploading to your destination S3 bucket.Log into the Source EC2 and install the AWS CLI.Run the AWS CLI sync command to upload the folder to S3:aws s3 sync /path/to/source/folder s3://my-shared-folder-bucket/folder/ --acl bucket-owner-full-controlStep 3: Download to the Destination EC2Log into your Destination EC2.Configure AWS CLI credentials with permission to read from your S3 bucket.Run the AWS CLI sync command to download the folder:aws s3 sync s3://my-shared-folder-bucket/folder/ /path/to/destination/folderMethod 2: Direct SCP via SSH (Best for Small, Ad-hoc Data)If both EC2 instances can communicate over the internet (have public IPs) and ports are open:Step 1: Configure Security GroupsIn the Destination EC2's Security Group, add an Inbound Rule allowing SSH (Port 22) from the Source EC2's IP address.Step 2: Copy the folderMove the Destination EC2's private key (.pem file) to the Source EC2.Log into the Source EC2 and run the scp command to push the folder directly:scp -i /path/to/destination-key.pem -r /path/to/source/folder ec2-user@<Destination-EC2-Public-IP>:/path/to/destination/folderIf you'd like, let me know:How large the folder is (e.g., MBs or GBs)?Are the instances in the same or different regions?I can help you determine the most cost-effective way to move the data.12 sitesData transfer between two AWS accounts? - RedditThis is possible via aws cli and sdks like boto3 for python. ... Wouldn't they need AWS Console access to actually upload the file...RedditIs there a way to transfer a file from one EC2 instance to ...Login to the first EC2 instance using SSH. Use the scp command to transfer the file to the second EC2 instance. The command should...AWS re:PostHow to copy files from one AWS account to Another AWS ...2 Answers. Sorted by: 3. You have 2 options: Pull files. Setup a bucket policy in your source granting read access to the user/rol...Stack OverflowShow allYou said: This is one time can I make s3 folder publicYes, you can make the S3 folder public for a one-time transfer, but it is highly discouraged due to data exposure risks.If you choose this route, you must explicitly disable AWS block public access settings first.Step 1: Unblock Public AccessOpen your S3 Bucket in the AWS Console.Go to the Permissions tab.Under Block public access (bucket settings), click Edit.Uncheck Block all public access and click Save changes (type "confirm").Step 2: Make the Folder Public via PolicyScroll down to Bucket policy on the same Permissions tab and click Edit.Paste this policy (replace your-bucket-name and your-folder/ with your actual names):json{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::your-bucket-name/your-folder/*"
        }
    ]
    
}
Use code with caution.Click Save changes.Step 3: Download on Destination EC2Log into your destination instance and download the files using standard HTTP tools like wget or curl:wget -r -np -nH --cut-dirs=1 https://amazonaws.comStep 4: Secure the Bucket (Crucial)Delete the files from the bucket immediately after download.Re-enable "Block all public access" in the bucket settings.Safer Alternative: Pre-signed URLsIf you want a quick link without opening your bucket to the public internet, use a Pre-signed URL. It creates a temporary download link that expires automatically.Zip the folder on your Source EC2.Upload the zip file to your private S3 bucket.Generate a temporary link via AWS CLI (expires in 1 hour):aws s3 presign s3://your-bucket-name/folder.zip --expires-in 3600Run wget "<generated_url>" on your Destination EC2.If you run into issues, tell me:What Operating System (Linux or Windows) your instances use.If you prefer to zip the folder first to speed up the transfer.I can provide the exact commands for your setup.26 sitesS3: is public access possible when Block all public access is on and object ownership is bucket owner enforced28 May 2025 — Remember that making S3 objects public is not recommended for most use cases due to security concerns. Consider if there are alter...AWS re:PostHow to Host a Static Website on S312 Feb 2026 — Step 3: Configure Public Access By default, S3 ( Amazon S3 ) blocks all public access. For a public website, you need to open it u...OneUptimeMake public a folder in an S3 bucket12 Apr 2023 — Make public a folder in an S3 bucket CHECK AWS BLOCK PUBLIC ACCESS SETTING (DEFAULTS CHANGING IN 2023) Sign in to the AWS Manageme...AWS re:PostShow allYou said: aws s3 sync /web/app/krost.com.au/public/media s3://krost-media-folder
fatal error: An error occurred (AccessDenied) when calling the ListObjectsV2 operation: User: arn:aws:sts::324037315773:assumed-role/krost-ecommerce_ec2_app_role/i-0965cc1047f1bed0c is not authorized to perform: s3:ListBucket on resource: "arn:aws:s3:::krost-media-folder" because no resource-based policy allows the s3:ListBucket actionThe error occurs because the destination bucket (Account B) is blocking the source EC2's IAM role (Account A) from viewing or writing to it.Because this is a cross-account transfer, the bucket policy in the destination account must explicitly grant access to your source EC2 role.The Fix: Update the Destination Bucket PolicyLog into the AWS Console of the Account that owns the S3 bucket (krost-media-folder).Go to S3 -> Click on krost-media-folder -> Go to the Permissions tab.Scroll down to Bucket policy and click Edit.Paste the following policy (this grants your exact source EC2 role full sync permissions):json{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "CrossAccountSyncList",
            "Effect": "Allow",
            "Principal": {
                "AWS": "arn:aws:iam::324037315773:role/krost-ecommerce_ec2_app_role"
            },
            "Action": [
                "s3:ListBucket",
                "s3:GetBucketLocation"
            ],
            "Resource": "arn:aws:s3:::krost-media-folder"
        },
        {
            "Sid": "CrossAccountSyncWrite",
            "Effect": "Allow",
            "Principal": {
                "AWS": "arn:aws:iam::324037315773:role/krost-ecommerce_ec2_app_role"
            },
            "Action": [
                "s3:PutObject",
                "s3:PutObjectAcl",
                "s3:GetObject"
            ],
            "Resource": "arn:aws:s3:::krost-media-folder/*"
        }
    ]
}
Use code with caution.Click Save changes.Step 2: Run the Sync Command AgainGo back to your source EC2 and run the command with the bucket-owner-full-control flag. This ensures the destination account actually owns the files you upload:bashaws s3 sync /web/app/krost.com.au/public/media s3://krost-media-folder --acl bucket-owner-full-control
Use code with caution.If you apply this policy and still get an error, let me know:Does the Source EC2 IAM role have an attached policy allowing s3:PutObject and s3:ListBucket outbound?Are you using AWS KMS encryption on the destination bucket?I can help adjust the permissions to clear any remaining blocks.