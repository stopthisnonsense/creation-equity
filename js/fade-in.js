window.addEventListener('load', () => {
	let options = {
		root: null,
		threshold: 0.33,
	};
	let callback = (entries, observer) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				// console.log(entry);
				entry.target.classList.add('js-fadein--toggled');
			}
		});
	};
	const fadeInObserver = new IntersectionObserver(callback, options);
	let entry_items = document.querySelectorAll('.js-fadein');
	entry_items.forEach((entry) => {
		fadeInObserver.observe(entry);
	});
});
