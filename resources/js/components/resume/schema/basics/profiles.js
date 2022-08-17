export default {
	fields: [
		// Network
		{
			type: 'input',
			inputType: 'text',
			placeholder: 'twitter',
			label: 'Network',
			model: 'network',
			styleClasses: ['col-12', 'col-md-4', 'p-0', 'pe-md-1'],
		},
		// URL
		{
			type: 'input',
			inputType: 'text',
			placeholder: 'https://twitter.com/user',
			label: 'Url',
			model: 'url',
			styleClasses: ['col-12', 'col-md-4', 'p-0', 'pe-md-1'],
		},
		// Username
		{
			type: 'input',
			inputType: 'text',
			placeholder: 'yourname',
			label: 'Username',
			model: 'username',
			styleClasses: ['col-12', 'col-md-4', 'p-0', 'pe-md-1'],
		},
	]
}