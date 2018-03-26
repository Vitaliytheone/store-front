require 'openssl'
require 'base64'


v1 = ARGV[0]
v2 = ARGV[1]

sha512 = OpenSSL::Digest::SHA512.new
nonce = nil
body = nil
method = 'GET'
request_uri = v1
secret = v2
constant_digest = sha512.digest("#{nonce}#{body}")
constant_digest == sha512.digest('')
constant_digest.bytes == [207, 131, 225, 53, 126, 239, 184, 189, 241, 84, 40, 80, 214, 109, 128, 7, 214, 32, 228, 5, 11, 87, 21, 220, 131, 244, 169, 33, 211, 108, 233, 206, 71, 208, 209, 60, 93, 133, 242, 176, 255, 131, 24, 210, 135, 126, 236, 47, 99, 185, 49, 189, 71, 65, 122, 129, 165, 56, 50, 122, 249, 39, 218, 62]
request = "#{method}#{request_uri}#{constant_digest}"
raw_signature = OpenSSL::HMAC.digest(sha512, secret, request)
raw_signature.bytes == [81, 229, 207, 43, 212, 101, 96, 81, 75, 117, 138, 86, 120, 96, 105, 73, 222, 14, 90, 201, 73, 71, 78, 250, 84, 20, 20, 226, 154, 201, 151, 51, 41, 123, 119, 246, 40, 190, 30, 81, 87, 233, 137, 159, 153, 247, 247, 59, 214, 218, 152, 102, 6, 22, 120, 131, 123, 240, 209, 53, 253, 41, 230, 203]
signature = Base64.strict_encode64(raw_signature)

print signature