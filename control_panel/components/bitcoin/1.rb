require 'base64'
require 'docopt'
require 'openssl'

v1 = ARGV[0]
v2 = ARGV[1]
# puts v1
# puts v2


sha512 = OpenSSL::Digest::SHA512.new

secret = v1
nonce = (Time.now.to_f * 1000).to_i
body = ''
nonce_body_hash = sha512.digest(nonce.to_s + body.to_s)
nonce_body_hash.bytes == [123, 43, 252, 100, 228, 170, 180, 74, 102, 78, 146, 144, 197, 246, 136, 25, 81, 207, 216, 218, 222, 86, 40, 184, 184, 181, 177, 204, 2, 160, 123, 2, 221, 81, 181, 97, 213, 106, 107, 213, 182, 25, 151, 12, 153, 7, 180, 215, 67, 66, 14, 202, 216, 115, 106, 18, 84, 221, 241, 253, 77, 104, 193, 203]
request = v2 + nonce_body_hash
raw_signature = OpenSSL::HMAC.digest(sha512, secret, request)
raw_signature.bytes == [166, 197, 147, 167, 160, 132, 102, 44, 80, 195, 253, 1, 47, 61, 213, 12, 204, 129, 177, 11, 243, 86, 156, 85, 166, 69, 180, 246, 80, 208, 21, 100, 104, 32, 236, 166, 179, 212, 8, 203, 113, 84, 43, 17, 176, 184, 147, 25, 117, 212, 236, 177, 165, 253, 146, 131, 240, 101, 232, 186, 46, 61, 35, 20]
signature = Base64.strict_encode64(raw_signature)

print nonce
print "x|||x"
print signature