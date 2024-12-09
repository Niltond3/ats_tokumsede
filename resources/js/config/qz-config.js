import certificate from "./assets/digital-certificate.txt";
import privateKey from "./assets/private-key.txt";

const CERTIFICATE =
    "-----BEGIN CERTIFICATE-----\n" +
    "MIIEHzCCAwegAwIBAgIUWTChik79/a1dla88xn8lRAq72RcwDQYJKoZIhvcNAQEL\n" +
    "BQAwgZ4xCzAJBgNVBAYTAkJSMRAwDgYDVQQIDAdwYXJhaWJhMQ8wDQYDVQQHDAZq\n" +
    "ZXJpY28xEjAQBgNVBAoMCVRvS3VtU2VkZTESMBAGA1UECwwJVG9LdW1TZWRlMRsw\n" +
    "GQYDVQQDDBIqLnRva3Vtc2VkZS5jb20uYnIxJzAlBgkqhkiG9w0BCQEWGGludGVy\n" +
    "bm9AdG9rdW1zZWRlLmNvbS5icjAeFw0yNDEyMDkxMDMyNDBaFw0zNDEyMDcxMDMy\n" +
    "NDBaMIGeMQswCQYDVQQGEwJCUjEQMA4GA1UECAwHcGFyYWliYTEPMA0GA1UEBwwG\n" +
    "amVyaWNvMRIwEAYDVQQKDAlUb0t1bVNlZGUxEjAQBgNVBAsMCVRvS3VtU2VkZTEb\n" +
    "MBkGA1UEAwwSKi50b2t1bXNlZGUuY29tLmJyMScwJQYJKoZIhvcNAQkBFhhpbnRl\n" +
    "cm5vQHRva3Vtc2VkZS5jb20uYnIwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEK\n" +
    "AoIBAQCqdP61d9D0wgXPWKsDOLB9zRJ2gqQXaAQiY4hOxdLzGLTSTSXcVmyZuQmN\n" +
    "eM2OK26Ywf3KAWSiEuRUiW7ejokUh22bnNTzbYLffM2pZmw7fc9ZjkoGvybYYt5z\n" +
    "ot6DP5MeKpO/KUa3u8FuUgASTRHl7LgwCoqqXpHiovR9JY9RIu8YFdzK0jIMaC3q\n" +
    "mvoSQDC4i0P3DD94KUSIbDAZsmwHRdd1Gu0psgMNrbSl5savGtUABT0ebHcAGju6\n" +
    "sXt8eXIGwewnYxYUR/VCKxUC/WBcnCsMGHv8x+BgnQOJM3t8hIr6w/Dcv83hKEZE\n" +
    "6Y1c+xlnP3qLe+yzNMkKXzF4wi7tAgMBAAGjUzBRMB0GA1UdDgQWBBRG71pbP+w/\n" +
    "G31Ok5fLQcgyMF9E+jAfBgNVHSMEGDAWgBRG71pbP+w/G31Ok5fLQcgyMF9E+jAP\n" +
    "BgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQCatPETInetRzMt9Wpy\n" +
    "c0hLlXppIenDrsxVJDDr8riaPEy/7AuOzBMrIcKIFhN/6z5q3C7uHgYPJ7u53BDA\n" +
    "XIydqwM6HOONsjlrYb74ojt6AVzAsDCFBOnmjK9r6aVFT6GFaOg+rFCdfUssCcYU\n" +
    "WJKSRoRRn39WYbcrAFlQlFe78zRZHlphTlQGgpwTsRlBRafz5Re1l5LokkXQuLd4\n" +
    "hcNynIlZQpEIjhXqW7MGwCK0WPMwqvv52okZU3w3CfuGpFs8KY3tYV/cFxmGWj4/\n" +
    "5Jp7VJ516CFphkproALbAeL6JucZmY8Q4Cf38I584gkv0QfKZUbXLeLJZ69cEtP8\n" +
    "zqbi\n" +
    "-----END CERTIFICATE-----";
const PRIVATE_KEY =
    "-----BEGIN PRIVATE KEY-----\n" +
    "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCqdP61d9D0wgXP\n" +
    "WKsDOLB9zRJ2gqQXaAQiY4hOxdLzGLTSTSXcVmyZuQmNeM2OK26Ywf3KAWSiEuRU\n" +
    "iW7ejokUh22bnNTzbYLffM2pZmw7fc9ZjkoGvybYYt5zot6DP5MeKpO/KUa3u8Fu\n" +
    "UgASTRHl7LgwCoqqXpHiovR9JY9RIu8YFdzK0jIMaC3qmvoSQDC4i0P3DD94KUSI\n" +
    "bDAZsmwHRdd1Gu0psgMNrbSl5savGtUABT0ebHcAGju6sXt8eXIGwewnYxYUR/VC\n" +
    "KxUC/WBcnCsMGHv8x+BgnQOJM3t8hIr6w/Dcv83hKEZE6Y1c+xlnP3qLe+yzNMkK\n" +
    "XzF4wi7tAgMBAAECggEATPx1U8GPQxRkal8aMeTHNbJK3fKoKRgmEeARXr6TRY9J\n" +
    "j9gfOvJfr1g2u8otUMJF+8FWHgfeCyNsM9A0nlkTCY4XD5rYRS+XVdf4zUNAq+fp\n" +
    "IAIXZg+sfDu/S8vqIq4yhIPnYgXC9lXbCbgIZzue01FEaRJByavXyNwff2xY2+Ql\n" +
    "V+ZLbM5xEjnOdpzIpI1EpMHUw2p/2ZYndpy//b+2GIuXaZCw6XbZqS2+PI1QofzB\n" +
    "h1Q3X8+9XscxHf2zEVbKmtLBryxL1xsZ2JjleF3+zPAS5nVvvbU/9xDYS4TuQlcK\n" +
    "1W1t8ljVrleI26UA3Iy6ctxcJjlKrZnAAOB3AEGcwwKBgQDkPm/36AR+eZD8r4DX\n" +
    "SEndVlLfYOV9ve68AV65RTpkF5ksyU/Ukr+D+C9nvE1UjfDZMMBvXaM7WUoukTId\n" +
    "VaUCm8Urzby/FRXwyZbZFaJmW23CRNysnweSv06gRa6XDy18Z3+/yLdJziuuLs7g\n" +
    "of5pUoeHYWvRLWWY7gpIENQ4RwKBgQC/L5F66VE1Gure+iowr0OfZLUAEFOJtELN\n" +
    "Fqwx0jxY1bfDcbYiyh/NUzUposYjCbxBmJWrDuWML0krh9/wkRlJ7KWcQUuAXWla\n" +
    "VaPf9lZjDsqPc3iyILHx2uID5iP0RhAzPxS/9VGnefqf8BpDjDJBD1YaBK7vULgl\n" +
    "im5dv2XtKwKBgGmedwPaKxI3wR8dO9rjicLR6oGR/kCkvq/jVmkXnwCoZupxse5l\n" +
    "hhhUpeM0IPjKwjRPOg2O4DVbiVdpOy6V7kj5ulEd4ITwBfop3xNPzpndHzpx0UhU\n" +
    "QxXmT0UdWcBvdpYF8vjVsfOGY3I0GOmcLPWiCjNcop7miggtxzY4C2yLAoGAcgLB\n" +
    "9NQyU0LsEXCJvGKoJuN9dL5HsvTGaVs98K/4wNkiLvEetnxmnqEiMOQa2EYz98Iw\n" +
    "bsQBa6m/LrBmgSVmOUlgMWBW0APkkbREd4iFV6k4bndj4IXS1/G5mq2hf0hQ3N6f\n" +
    "VyHKQd9h8ALVBrcSOO4WYImucJRoXCrDSjCbbeUCgYBJLeoEEFMdey68AZ32fjSG\n" +
    "X6ZjGFPpX75ydkwZLws8Jpej9fJL/N8gbHruzQCnmRwSFTlnypVR0WoSruW2DIIv\n" +
    "A8u/LmlfsEenq6phQdUGcsunDS7gKlAFqxAzWGALXQavbYiOGiMT0xP4H4+2AcrX\n" +
    "Gg479p5mJY9X4mTYO3lS5g==\n" +
    "-----END PRIVATE KEY-----";

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
