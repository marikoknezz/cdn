
# Disable http authentication for VSHosting CDN's IPs - https://support.vshosting.cz/content/31/33/cs/cdn-%C5%99esen%C3%AD.html
if [ ${RUNNING_PRODUCTION} -eq "0" ]; then
    yq write --inplace "${CONFIGURATION_TARGET_PATH}/ingress/.ingress.yaml" metadata.annotations."\"nginx.ingress.kubernetes.io/configuration-snippet\"" "satisfy any;
allow 93.185.110.99/32;
allow 93.185.110.100/32;
allow 93.185.110.101/32;
allow 185.198.191.147/32;
allow 204.145.66.226/32;
allow 77.81.119.26/32;
allow 86.105.155.150/32;
allow 185.115.0.0/24;
allow 77.247.124.1/32;
deny all;"
fi
