Workflow:
1. Create test accounts using MetaMask
	- Owner address: 0xDBd18e5B41B25c81f4065286C788d790f7A42607
	- Client address: 0x32A6Ba84512aB6053fc65a91cB02791AC84B9bF2

2. Create TokenContract. Uploaded it using remix.ethereum.org/
	- Contract address v1: 0x3B2f7Cb8bC1F95885E5eE8bd09775e7A9C731E9c
	- Contract address v2: 0x399211E561bb55E0110A6db86C194b98bcFfaf6e
	- Contract address v3: 0x92516B9997fF4c987BfAE6fdcbB7E6bE787Be931
	- Contract address v4: 0x0BD4b82ED8c855d6F2f7d60956510648Ab0d8B9B
	- Token Details:
		- Name: EastWind
		- Symbol: EW1

3. Set up local test-bed using node. 
	1. Create local directory and put all the files inside.
	2. npm init -y
	3. npm i -D truffle
	4. truffle init
	5. npm i -D webpack webpack-dev-server react react-dom babel-core babel-loader babel-preset-react babel-preset-es2015 babel-preset-stage-2 css-loader style-loader json-loader web3
	6. npm run dev to build files
	7. npm run start to start serving files
	9. Go to http://127.0.0.1:8010. You should now be able to see the running page. 

4. The page will use the MetaMask account and network to send Tokens to the Receiveing Address.
	- You can verify the transaction by going to https://ropsten.etherscan.io/address/0x0BD4b82ED8c855d6F2f7d60956510648Ab0d8B9B
