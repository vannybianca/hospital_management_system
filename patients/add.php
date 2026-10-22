<?php require_once "../config/database.php"; $error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
$date_of_birth=$_POST['date_of_birth']!==''?$_POST['date_of_birth']:null;
$s=$conn->prepare("INSERT INTO patients(patient_number,first_name,last_name,gender,date_of_birth,phone,email,address,emergency_contact,emergency_phone,blood_group) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
$s->bind_param("sssssssssss",$_POST['patient_number'],$_POST['first_name'],$_POST['last_name'],$_POST['gender'],$date_of_birth,$_POST['phone'],$_POST['email'],$_POST['address'],$_POST['emergency_contact'],$_POST['emergency_phone'],$_POST['blood_group']);
if($s->execute()){header("Location: index.php");exit;} $error=$s->error;} ?>
<!doctype html><html><head><title>Register Patient</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="container"><h1>Register Patient</h1>
<?php if($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?><form method="post">
<label>Patient Number</label><input name="patient_number" required><label>First Name</label><input name="first_name" required><label>Last Name</label><input name="last_name" required>
<label>Gender</label><select name="gender" required><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select>
<label>Date of Birth</label><input type="date" name="date_of_birth"><label>Phone</label><input name="phone"><label>Email</label><input type="email" name="email"><label>Address</label><input name="address">
<label>Emergency Contact</label><input name="emergency_contact"><label>Emergency Phone</label><input name="emergency_phone"><label>Blood Group</label><input name="blood_group">
<button type="submit">Register Patient</button></form><a href="index.php">← Back</a></div></body></html>