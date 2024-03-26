membership-signup is a docker image that include the Docker-Lamp baseimage (Ubuntu 22.04), along with a LAMP stack ([Apache][apache], [MySQL][mysql] and [PHP][php]) all in one handy package.

1. With Ubuntu **22.04** image on the `latest-2204`, membership-signup is ready to test the Membership Signup app

# To build a new docker image  
sudo docker build -t=softcoder/membership-signup:latest -f ./docker/2204/Dockerfile .  

# To run the docker image (set environment variables to match your values)
sudo docker run -p "80:80" -e APP_SMTP_OutboundUsername='myemail@gmail.com' -e APP_SMTP_OutboundPassword='xx123' -e APP_SMTP_OutboundFromAddress='myemail@gmail.com' softcoder/membership-signup:latest  

#ENV vars:
ENV APP_SMTP_OutboundHost "smtp.googlemail.com"
ENV APP_SMTP_OutboundPort 587
ENV APP_SMTP_OutboundEncrypt "tls"
ENV APP_SMTP_OutboundAuth true
ENV APP_SMTP_OutboundUsername "X@gmail.com"
ENV APP_SMTP_OutboundPassword "XX"
ENV APP_SMTP_OutboundFromAddress "X@gmail.com"
ENV APP_SMTP_OutboundFromName "Membership Signup"

ENV APP_WEBSITE_Name "Local Test"
ENV APP_WEBSITE_RootURL "/"
ENV APP_WEBSITE_Timezone "America/Vancouver"

ENV APP_PDF_OutputPath "output/"
ENV APP_PDF_Membershipfile "forms/MembershipForm2024-2025.pdf"
ENV APP_PDF_MembershipfileEmailViewTemplate "MembershipForm.pdf"
ENV APP_PDF_Waiverfile "forms/E-waiver-FMCBC-Universal-Waiver-Basic-2022.pdf"
ENV APP_PDF_WaiverfileEmailViewTemplate "E-waiver-FMCBC-Universal-Waiver-Basic.pdf"
ENV APP_PDF_WebformEmailField "emailaddress"
ENV APP_PDF_PDFTKPath ""
ENV APP_PDF_EmailPDFToMember true
#comma separated list of people to email a copy of the form
ENV APP_PDF_EmailPDFToDirectors ""
ENV APP_PDF_FormsDateRange "May 1, 2024 - April 30, 2025"

ENV APP_TWOFA_Enabled true
ENV APP_TWOFA_TotpKey "DOCKER23MRL5AUQNK3G"
# 60*45
ENV APP_TWOFA_TotpTimeoutSeconds 2700

# To push the docker image to dockerhub  
sudo docker push softcoder/membership-signup:latest  
  
---  

2. Google Cloud Run:  

There is a seperate Dockerfile for deploying to Google's Serverless Cloud Run platform.  
The Dockerfile is located in the app folder. The docker image produced does not include a db engine  
it is assumed you have setup Google Cloud SQL (MySQL). In your cloud run service, ensure the live   
revision has the following environment varialbes set to connect to your environment:  

Environment variables  
see APP ENV vars above

Name: APP_GOOGLE_MAP_API_KEY  
Value: <your api key>  

To see more variables look in the config.php file in the dcoker/app folder  

Open a terminal and navigate into the 'docker/app' folder (make sure NOT to run from the docker folder)  

# Authentication your google cloud platform account
gcloud auth login  

# Build the docker Image in google cloud run  
gcloud builds submit --tag gcr.io/pgtg-container-demo/membership-signup  

On Success you will see something like:  
...  
ID                CREATE_TIME               DURATION SOURCE                                               IMAGES                                          STATUS  
e53b2c57-697b-... 2019-08-22T07:10:32+00:00 2M60S    gs://pgtg-container-demo_cloudbuild/source/1566..tgz gcr.io/pgtg-container-demo/membership-signup (+1 more)  SUCCESS  

# Deploy the image to make it live (notice the last parameter shows how you can pass the env var via commandline)  
gcloud beta run deploy --image gcr.io/pgtg-container-demo/membership-signup --platform managed \
       --update-env-vars APP_SMTP_OutboundUsername='myemail@gmail.com' APP_SMTP_OutboundPassword='xx123' APP_SMTP_OutboundFromAddress='myemail@gmail.com' APP_GOOGLE_MAP_API_KEY=<your api key>  

FYI important links regarding Google Cloud run:  

https://cloud.google.com/sql/docs/mysql/quickstart  
https://stackoverflow.com/questions/56342904/enter-a-docker-container-running-with-google-cloud-run  

