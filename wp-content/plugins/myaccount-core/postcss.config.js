module.exports = {
	// Generate source maps in dev so DevTools shows original file (e.g. base.css, navigation.css)
	// inline: false = output separate .map file (myaccount.css.map) instead of embedding
	map: process.env.NODE_ENV !== 'production' ? { inline: false } : false,
	plugins: [
		require('postcss-import'),
		require('postcss-nested-ancestors'),
		require('tailwindcss/nesting'),
		require('tailwindcss')
	]
};
