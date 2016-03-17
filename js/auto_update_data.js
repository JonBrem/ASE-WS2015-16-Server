/**
 * The AutoUpdateData class / function is useful for all the automatic updates the site needs.
 * <br>This is like an extremely lightweight part of the automatic ViewModel-parts of Frameworks such as Angular
 * (not anywhere near as complex, but this might explain why we use it.)
 * <br>Basically, you bind a change listener to it and the AutoUpdateData object tells you what changed when you
 * give it its new values in the update method.
 */
var AutoUpdateData = function(modelData) {
	var publ = {};
	var changeListeners = [];

	var data = {};
	for(var key in modelData) {
		data[key] = modelData[key];
	}

	var update = function(newData) {
		for(var key in newData) {
			if(key in data) {
				if(!deepCompare(data[key], newData[key])) {
					data[key] = newData[key];
					change(key, data[key]);
				}
			} else {
				data[key] = newData[key];
				change(key, data[key]);
			}
		}
	};

	var deepCompare = function(var1, var2) {
		if(var1 == null && var2 == null) return true;
		if(var1 == null && var2 != null) return false;
		if(var1 != null && var2 == null) return false;
		if(var1.constructor === Object && var2.constructor !== Object) return false;
		if(var1.constructor !== Object && var2.constructor === Object) return false;
		if(var1.constructor === Object && var2.constructor === Object) return deepCompareObject(var1, var2);

		if(var1.constructor === Array && var2.constructor !== Array) return false;
		if(var1.constructor !== Array && var2.constructor === Array) return false;
		if(var1.constructor === Array && var2.constructor === Array) return deepCompareArray(var1, var2);

		else if(var1 == var2) return true;

		return false;
	};

	var deepCompareObject = function(obj1, obj2) {
		for(var key in obj1) {
			if(key in obj2) {
				if(!deepCompare(obj1[key], obj2[key])) return false;
			} else {
				return false;
			}
		}

		for(var key in obj2) {
			if(key in obj1) {
				if(!deepCompare(obj1[key], obj2[key])) return false;
			} else {
				return false;
			}
		}

		return true;
	};

	var deepCompareArray = function(arr1, arr2) {
		if(arr1.length != arr2.length) return false;
		for(var i = 0; i < arr1.length; i++) {
			if(!deepCompare(arr1[i], arr2[i])) {
				return false;
			}
		}
		return true;
	};

	var getData = function() {
		return data;
	};

	var registerChangeListener = function(onChangeListener) {
		changeListeners.push(onChangeListener);
	};

	var change = function(what, value) {
		for(var i = 0; i < changeListeners.length; i++) {
			changeListeners[i]({"what": what, "value": value});
		}
	};

	var destroy = function() {
		for(var i = 0; i < changeListeners.length; i++) {
			changeListeners[i]({"what": "destroy"});
		}		
	};

	publ.registerChangeListener = registerChangeListener;
	publ.getData = getData;
	publ.update = update;
	publ.destroy = destroy;
	return publ;
};