import React from 'react'
import ReactDOM from 'react-dom'
import Web3 from 'web3'
import './../css/index.css'
import Math from 'mathjs'

class App extends React.Component {

	constructor(props){
		super(props)

		this.state = {
			interval: 3000,
			contractAddress : "0x0BD4b82ED8c855d6F2f7d60956510648Ab0d8B9B",
			ownerWallet:"0xDBd18e5B41B25c81f4065286C788d790f7A42607",
			userWallet:"",
			url : "https://api.coinmarketcap.com/v1/ticker/ethereum/",
			etherPrice: 0,
			tokenPrice: 0,
			actualTokenPrice: 0,
			sellPrice: 0,
			buyPrice: 0,
			tokenToUsd: 30,
			tokenBuyAmount: 0,
			tokenSellAmount: 0,
			etherAmount: 0,
			tokenInAccount: 0,
			receiverAddress: '',
			amount: 2
		};		

    	this.handleInputChange = this.handleInputChange.bind(this);
		this.fetchdata = this.fetchdata.bind(this);
		this.buyToken = this.buyToken.bind(this);
		this.sellToken = this.sellToken.bind(this);

		const MyContract = web3.eth.contract([
		{
			"constant": true,
			"inputs": [],
			"name": "mintingFinished",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "totalSupply",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "symbol",
			"outputs": [
				{
					"name": "",
					"type": "string"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [
				{
					"name": "_owner",
					"type": "address"
				}
			],
			"name": "balanceOf",
			"outputs": [
				{
					"name": "balance",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [
				{
					"name": "_owner",
					"type": "address"
				},
				{
					"name": "_spender",
					"type": "address"
				}
			],
			"name": "allowance",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "buyPrice",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "paused",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "decimals",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "owner",
			"outputs": [
				{
					"name": "",
					"type": "address"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "name",
			"outputs": [
				{
					"name": "",
					"type": "string"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "_totalSupply",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": true,
			"inputs": [],
			"name": "sellPrice",
			"outputs": [
				{
					"name": "",
					"type": "uint256"
				}
			],
			"payable": false,
			"stateMutability": "view",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_spender",
					"type": "address"
				},
				{
					"name": "_subtractedValue",
					"type": "uint256"
				}
			],
			"name": "decreaseApproval",
			"outputs": [
				{
					"name": "success",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"anonymous": false,
			"inputs": [
				{
					"indexed": true,
					"name": "owner",
					"type": "address"
				},
				{
					"indexed": true,
					"name": "spender",
					"type": "address"
				},
				{
					"indexed": false,
					"name": "value",
					"type": "uint256"
				}
			],
			"name": "Approval",
			"type": "event"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_to",
					"type": "address"
				},
				{
					"name": "_value",
					"type": "uint256"
				}
			],
			"name": "transfer",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [],
			"name": "unpause",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_from",
					"type": "address"
				},
				{
					"name": "_to",
					"type": "address"
				},
				{
					"name": "_value",
					"type": "uint256"
				}
			],
			"name": "transferFrom",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_to",
					"type": "address"
				},
				{
					"name": "_amount",
					"type": "uint256"
				}
			],
			"name": "mint",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"anonymous": false,
			"inputs": [],
			"name": "Unpause",
			"type": "event"
		},
		{
			"constant": false,
			"inputs": [],
			"name": "finishMinting",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"anonymous": false,
			"inputs": [
				{
					"indexed": true,
					"name": "from",
					"type": "address"
				},
				{
					"indexed": true,
					"name": "to",
					"type": "address"
				},
				{
					"indexed": false,
					"name": "value",
					"type": "uint256"
				}
			],
			"name": "Transfer",
			"type": "event"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "newOwner",
					"type": "address"
				}
			],
			"name": "transferOwnership",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"inputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "constructor"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_token",
					"type": "address"
				}
			],
			"name": "claimTokens",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"anonymous": false,
			"inputs": [],
			"name": "Pause",
			"type": "event"
		},
		{
			"constant": false,
			"inputs": [],
			"name": "buy",
			"outputs": [],
			"payable": true,
			"stateMutability": "payable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_spender",
					"type": "address"
				},
				{
					"name": "_value",
					"type": "uint256"
				}
			],
			"name": "approve",
			"outputs": [
				{
					"name": "",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"anonymous": false,
			"inputs": [
				{
					"indexed": true,
					"name": "_token",
					"type": "address"
				},
				{
					"indexed": true,
					"name": "_controller",
					"type": "address"
				},
				{
					"indexed": false,
					"name": "_amount",
					"type": "uint256"
				}
			],
			"name": "ClaimedTokens",
			"type": "event"
		},
		{
			"anonymous": false,
			"inputs": [
				{
					"indexed": true,
					"name": "to",
					"type": "address"
				},
				{
					"indexed": false,
					"name": "amount",
					"type": "uint256"
				}
			],
			"name": "Mint",
			"type": "event"
		},
		{
			"anonymous": false,
			"inputs": [],
			"name": "MintFinished",
			"type": "event"
		},
		{
			"anonymous": false,
			"inputs": [
				{
					"indexed": true,
					"name": "previousOwner",
					"type": "address"
				},
				{
					"indexed": true,
					"name": "newOwner",
					"type": "address"
				}
			],
			"name": "OwnershipTransferred",
			"type": "event"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "_spender",
					"type": "address"
				},
				{
					"name": "_addedValue",
					"type": "uint256"
				}
			],
			"name": "increaseApproval",
			"outputs": [
				{
					"name": "success",
					"type": "bool"
				}
			],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "newSellPrice",
					"type": "uint256"
				},
				{
					"name": "newBuyPrice",
					"type": "uint256"
				}
			],
			"name": "setPrices",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [
				{
					"name": "amount",
					"type": "uint256"
				}
			],
			"name": "sell",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [],
			"name": "pause",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		},
		{
			"constant": false,
			"inputs": [],
			"name": "disown",
			"outputs": [],
			"payable": false,
			"stateMutability": "nonpayable",
			"type": "function"
		}
		])

		this.ContractInstance = MyContract.at(this.state.contractAddress);

   }

   fetchdata(){
		fetch(this.state.url)
		  .then(response => response.json())
		  .then(json => {
		  	let priceToUsd = parseFloat(json[0]["price_usd"] );

			this.setState({ etherPrice: priceToUsd });
			this.setState({ actualTokenPrice: this.state.tokenToUsd/priceToUsd});	//Pegged to actual Ether Price
			var this2 = this;
			this.ContractInstance.buyPrice(function(error, result){
								if(!error){					
									this2.setState({ tokenPrice: result.c});
								}
								else
								console.error(error);
							});

			this.ContractInstance.balanceOf(this.state.userWallet, function(error, result){
								if(!error){					
									let tokens = result.c;
									this2.setState({ tokenInAccount: tokens[0]});
								}
								else
								console.error(error);
							});
			/*
			this.ContractInstance.balanceOf(web3.eth.accounts[0], function(error, result){
								if(!error){					
									let tokens = result.c;
									this2.setState({ tokenInAccount: tokens[0]});
								}
								else
								console.error(error);
							});
							*/
		});		   	
   }

	componentDidMount() {
		setInterval(this.fetchdata, parseInt(this.state.interval));
	}	

	buyToken(){
		// let exactEtherAmount = Math.floor(this.state.etherAmount/ this.state.tokenPrice) * this.state.tokenPrice; //Do not over spend ether.
		// this.setState({etherAmount: exactEtherAmount}, function(){
		// 	this.ContractInstance.buy({from: web3.eth.accounts[0], gas: 3000000, value: this.state.etherAmount}, function(error, result){
		// 		if(!error)
		// 		console.log(result)
		// 		else
		// 		console.error(error);
		// 	})
		// });
		let exactEtherAmount = this.state.tokenBuyAmount* this.state.actualTokenPrice;
		web3.eth.sendTransaction({from: this.state.userWallet, to: this.state.ownerWallet, value: web3.toWei(exactEtherAmount,"ether")}, function(error, result){
			if(!error)
			console.log(result)
			else
			console.error(error);
		});
	}

	sellToken(){
		this.ContractInstance.sell(this.state.tokenSellAmount, function(error, result){
			if(!error)
				console.log(result)
				else
				console.error(error);
			}) 
	}
	handleInputChange(event) {
	    const target = event.target;
	    const value =  target.value;
	    const name = target.name;

	    this.setState({
	      [name]: value
	    });
	    if (target.name === "etherAmount") this.setState({tokenBuyAmount: Math.floor(value/this.state.tokenPrice)})
  	}


   render(){
	return (
		<div>
			<h2> Hastings Account </h2><br/>
			<div> 
				<span>Hasting Contract Address: {this.state.contractAddress}</span> <br/>
				<span> Hasting Wallet Address: {this.state.ownerWallet} </span><br/>
				<span> Number of EW Tokens (Total): 2500000 </span><br/>
				<span> Number of EW Tokens (Left): 1500000 </span> <br/>
				<span> Cost of EW Token (1 MAR 2018): USD {this.state.tokenToUsd} or ETH {this.state.actualTokenPrice} </span> <br/>
			</div>
			<hr/>			
			<h2> My Account </h2><br/>
			<div> 
				<label>
					User Wallet Address:
					<input
						name="userWallet"
						type="text"
						value={this.state.userWallet}
						onChange={this.handleInputChange}></input>
				</label>
			</div>
			<div> Number of EW Token: {this.state.tokenInAccount}</div>
			<hr/>
			<div>
				<label>
					Number of Tokens to Buy:
					<input
						name="tokenBuyAmount"
						type="number"
						step="0.01"
						value={this.state.tokenBuyAmount}
						onChange={this.handleInputChange}></input>
				</label>			
			</div>
			<div> Cost in USD: {this.state.tokenBuyAmount* this.state.tokenToUsd } </div>
			<div> Cost in ETH: {this.state.tokenBuyAmount* this.state.actualTokenPrice } </div>
			<button onClick={this.buyToken}>Buy Token</button>
			<div>
				<label>
					Sell Tokens:
					<input
						name="tokenSellAmount"
						type="text"
						value={this.state.tokenSellAmount}
						onChange={this.handleInputChange}></input>
				</label>
				<button onClick={this.sellToken}>Sell Token</button>
				<br></br>											
			</div>
		</div>
   	);
   }
}

ReactDOM.render(
   <App />,
   document.querySelector('#root')
)
