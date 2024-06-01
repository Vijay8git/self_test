<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Signal Control</title>
    <style>
        .signal { margin: 10px; padding: 20px; display: inline-block; width: 50px; height: 50px; text-align: center; line-height: 50px; color: white; font-weight: bold; }
        .green { background-color: green; }
        .yellow { background-color: yellow; color: black; }
        .red { background-color: red; }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <h1>Traffic Signal Control</h1>
    <form id="signalForm">
        <label for="sequence1">Signal 1 (A):</label>
        <input type="number" id="sequence1" name="sequence1" min="1" max="4" value="<?php echo isset($sequence[0]) ? $sequence[0] : ''; ?>" required><br><br>

        <label for="sequence2">Signal 2 (B):</label>
        <input type="number" id="sequence2" name="sequence2" min="1" max="4" value="<?php echo isset($sequence[1]) ? $sequence[1] : ''; ?>" required><br><br>

        <label for="sequence3">Signal 3 (C):</label>
        <input type="number" id="sequence3" name="sequence3" min="1" max="4" value="<?php echo isset($sequence[2]) ? $sequence[2] : ''; ?>" required><br><br>

        <label for="sequence4">Signal 4 (D):</label>
        <input type="number" id="sequence4" name="sequence4" min="1" max="4" value="<?php echo isset($sequence[3]) ? $sequence[3] : ''; ?>" required><br><br>

        <label for="greenInterval">Green Interval (seconds):</label>
        <input type="number" id="greenInterval" name="greenInterval" value="<?php echo $green_interval; ?>" required><br><br>

        <label for="yellowInterval">Yellow Interval (seconds):</label>
        <input type="number" id="yellowInterval" name="yellowInterval" value="<?php echo $yellow_interval; ?>" required><br><br>

        <button type="button" onclick="startSignal()">Start</button>
        <button type="button" onclick="stopSignal()">Stop</button>
    </form>

    <div id="signals">
		<div id="signal1" class="signal red">A</div>
        <div id="signal2" class="signal red">B</div>
        <div id="signal3" class="signal red">C</div>
        <div id="signal4" class="signal red">D</div>
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
			currentInterval--;
			console.log(currentInterval);
			if (currentInterval <= 0) {
				// Move to the next state
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
