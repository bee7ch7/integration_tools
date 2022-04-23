php -f C:\inetpub\wwwroot\integrations\gcp\lmorder_to_gcp\extractor.php

ping 127.0.0.1 -n 10 > nul

php -f C:\inetpub\wwwroot\integrations\gcp\lmorder_to_gcp\uploadToBucketLMORDER.php

