(function() {
  var DPLOY, Deploy, Generator, Help, Version, colors,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  colors = require("colors");

  Deploy = require("./deploy");

  Generator = require("./generator");

  Help = require("./help");

  Version = require("./version");

  module.exports = DPLOY = (function() {
    DPLOY.prototype.servers = null;

    DPLOY.prototype.connection = null;

    DPLOY.prototype.ignoreInclude = false;

    /*
    	DPLOY
    	If you set a custom config file for DPLOY
    	It will use this config instead of trying to load a dploy.yaml file
    	
    	@param 	config (optional)		Custom config file of a server to deploy at
    	@param 	completed (optional)	Callback for when the entire proccess is completed
    */


    function DPLOY(config, completed) {
      this.config = config;
      this.completed = completed;
      this.deploy = __bind(this.deploy, this);
      if (this.config) {
        this.servers = [null];
        return this.deploy();
      } else if (process.argv.indexOf("install") >= 0) {
        return new Generator();
      } else if (process.argv.indexOf("--help") >= 0 || process.argv.indexOf("-h") >= 0) {
        return new Help();
      } else if (process.argv.indexOf("--version") >= 0 || process.argv.indexOf("-v") >= 0) {
        return new Version();
      } else {
        this.servers = process.argv.splice(2, process.argv.length);
        this.ignoreInclude = this.servers.indexOf("-i") >= 0 || this.servers.indexOf("--ignore-include") >= 0;
        this.servers = this.servers.filter(function(value) {
          return value !== "-i" && value !== "--ignore-include";
        });
        if (this.servers.length === 0) {
          this.servers.push(null);
        }
        this.deploy();
      }
    }

    DPLOY.prototype.deploy = function() {
      var code;
      if (this.connection) {
        this.connection.dispose();
        this.connection = null;
      }
      if (this.servers.length) {
        this.connection = new Deploy(this.config, this.servers[0], this.ignoreInclude);
        this.connection.completed.add(this.deploy);
        this.servers.shift();
      } else {
        console.log("All Completed :)".green.bold);
        if (this.completed) {
          this.completed.call(this);
        } else {
          process.exit(code = 0);
        }
      }
      return this;
    };

    return DPLOY;

  })();

}).call(this);
