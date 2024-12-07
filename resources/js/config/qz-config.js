import certificate from "./assets/digital-certificate.txt";
import privateKey from "./assets/private-key.pem";

const CERTIFICATE = certificate;
const PRIVATE_KEY = privateKey;

export const QZ_CONFIG = {
    host: "localhost",
    port: 8182,
    keepAlive: true,
    retries: 3,
    delay: 1,
};

export const QZ_SECURITY = {
    certificate: CERTIFICATE,
    privateKey: PRIVATE_KEY,
};
