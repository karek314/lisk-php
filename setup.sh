sudo apt-get install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
apt-get remove libsodium-dev
apt-get install php-pear php-bcmath php-memcached php-curl php-mcrypt memcached php7.1-dev php-gmp -y
cd lib
git clone https://github.com/jedisct1/libsodium --branch stable
cd libsodium
./configure
make && make check
make install
pecl install libsodium
cd ..
if ! grep -q sodium.so $(php --ini | grep Loaded | cut -d" " -f12); then
	echo "extension=sodium.so" >> $(php --ini | grep Loaded | cut -d" " -f12)
fi
git clone https://github.com/karek314/bytebuffer/
