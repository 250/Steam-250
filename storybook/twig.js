const {TwingEnvironment, TwingLoaderRelativeFilesystem} = require('twing');

module.exports = new TwingEnvironment(new TwingLoaderRelativeFilesystem());
