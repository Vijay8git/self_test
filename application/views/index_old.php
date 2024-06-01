<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Signal Test</title>
    <style>
        /* Add your styles here */
        .signal { margin: 10px; padding: 20px; display: inline-block; }
        .green { background-color: green; }
        .yellow { background-color: yellow; }
        .red { background-color: red; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Traffic Signal Control</h1>
    <form id="signalForm">
        <label for="sequence">Signal Sequence (comma-separated, e.g., A,B,C,D):</label>
        <input type="text" id="sequence" name="sequence" required><br><br>

        <label for="greenInterval">Green Interval (seconds):</label>
        <input type="number" id="greenInterval" name="greenInterval" required><br><br>

        <label for="yellowInterval">Yellow Interval (seconds):</label>
        <input type="number" id="yellowInterval" name="yellowInterval" required><br><br>

        <button type="button" onclick="startSignal()">Start</button>
        <button type="button" onclick="stopSignal()">Stop</button>
    </form>

    <div id="signals">
        <div id="signalA" class="signal red">A</div>
        <div id="signalB" class="signal red">B</div>
        <div id="signalC" class="signal red">C</div>
        <div id="signalD" class="signal red">D</div>
    </div>

    <script>
        let currentSignalIndex = 0;
		let currentSignalState = 'red';
		let currentInterval = 0;
		let greenInterval, yellowInterval, sequence;
		let intervalId;

		function startSignal() {
			const formData = $('#signalForm').serialize();
			$.post('traffic/start', formData, function(data) {
				if (data.success) {
					sequence = data.sequence;
					greenInterval = data.greenInterval;
					yellowInterval = data.yellowInterval;
					currentSignalIndex = 0;
					currentSignalState = 'green';
					currentInterval = greenInterval;
					updateSignalState(); // Initial update
					intervalId = setInterval(updateSignalState, 1000);
				} else {
					alert(data.message);
				}
			}, 'json');
		}

		function stopSignal() {
			clearInterval(intervalId);
			$.post('traffic/stop', {}, function(data) {
				if (data.success) {
					resetSignals();
				}
			}, 'json');
		}

		function updateSignalState() {
			// Decrement the current interval timer
			//alert('currentInterval = '+currentInterval);return false;
			currentInterval--;
			// alert('currentInterval = '+currentInterval);
			console.log(currentInterval);
			if (currentInterval <= 0) {
				// Move to the next state
				// alert('currentSignalState = '+currentInterval);
				switch (currentSignalState) {
					case 'green':
						currentSignalState = 'yellow';
						currentInterval = yellowInterval;
						setSignalColor(sequence[currentSignalIndex], 'yellow');
						break;
					case 'yellow':
						currentSignalState = 'red';
						currentInterval = 1; // Red state change will be immediate
						setSignalColor(sequence[currentSignalIndex], 'red');
						// Move to the next signal in the sequence
						currentSignalIndex = (currentSignalIndex + 1) % sequence.length;
						currentSignalState = 'green';
						currentInterval = greenInterval;
						setSignalColor(sequence[currentSignalIndex], 'green');
						break;
					case 'red':
						// This case will not occur as we immediately switch to green after red
						break;
				}
			}

			// alert('last = ');
		}

		function setSignalColor(signal, color) {
			// Reset all signals to red first
			$('.signal').removeClass('green yellow').addClass('red');
			// Set the specified signal to the given color
			$('#signal' + signal).removeClass('red').addClass(color);
		}

		function resetSignals() {
			$('.signal').removeClass('green yellow').addClass('red');
		}
    </script>
</body>
</html>
