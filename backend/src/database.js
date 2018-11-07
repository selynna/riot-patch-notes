var express    = require("express");
var mysql      = require('mysql');

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '',
  database : 'riotpatchnotes'
});
var app = express();

connection.connect(function(err){
	if(!err){
		console.log("(DEBUG) db connected");    
	}else{
		console.log("(DEBUG) db connection error");    
	}
});

exports.getAllPatches = function(){
	return new Promise((resolve, reject) => {
		connection.query('SELECT * from patch ORDER BY id DESC', function(err, rows, fields){
			connection.end();
			if(!err){
//				console.log('(DEBUG) result: ', rows);
				resolve(rows);
			}else{
//				console.log('(DEBUG) db query error: ' + err);
				reject();
			}
		});
	})
};

exports.getAllChangesForChampionId = function(championId){
	return new Promise((resolve, reject) => {
		connection.query('SELECT * from patch_champion_changes WHERE championId = ? ORDER BY patchId DESC', [championId], function(err, rows, fields){
			connection.end();
			if(!err){
//				console.log('(DEBUG) result: ', rows);
				resolve(rows);
			}else{
//				console.log('(DEBUG) db query error: ' + err);
				reject();
			}
		});
	})
};

exports.getAllChangesForChampionIdAfterDate = function(championId, date){
	return new Promise((resolve, reject) => {
		connection.query('SELECT * from patch_champion_changes JOIN patch ON (patch.id = patch_champion_changes.patchId) WHERE championId = ? AND date > ? ORDER BY patchId DESC', [championId, date], function(err, rows, fields){
			connection.end();
			if(!err){
//				console.log('(DEBUG) result: ', rows);
				resolve(rows);
			}else{
//				console.log('(DEBUG) db query error: ' + err);
				reject();
			}
		});
	})
};

exports.getAllChangesForChampionIdAfterDate(77, new Date('October 1, 2018 00:00:00')).then((response) => {
	console.log(response);
});