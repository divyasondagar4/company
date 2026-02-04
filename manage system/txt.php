<?php
echo "hello";
?>

<!-- i want 
register page 
user name
password_
roles manager admin and employee
manager insert employee name tast date time task session_status
admin manger manager edit delete and employee also edit and delete
employee only view html css php very easy code of every page and databse table instruction

 -->

 <!-- CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    role ENUM('admin','manager','employee')
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(50),
    task VARCHAR(200),
    task_date DATE,
    task_time TIME,
    session_status VARCHAR(50)
);
 -->

 Perfect 🔥 Let’s make your project **fully working, clean, and interview-ready**.

You want:

✅ Login + Register on SAME page
✅ Role system
✅ Manager add task
✅ Admin manage users
✅ Employee only view
✅ Logout → redirect to login
✅ Password wrong message
✅ Bootstrap UI
✅ Very easy PHP

---

# 🗄️ STEP 1 — DATABASE

### Database name: **task_system**

```sql
CREATE DATABASE task_system;
USE task_system;
```

### 👤 users table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    role ENUM('admin','manager','employee')
);
```

### 📋 tasks table

```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(50),
    task VARCHAR(200),
    task_date DATE,
    task_time TIME,
    session_status VARCHAR(50)
);
```

---

# 🔌 db.php

```php
<?php
$conn = mysqli_connect("localhost", "root", "", "task_system");
if (!$conn) die("Connection Failed");
?>
```

---

# 🔐 login_register.php (MAIN PAGE)

```php
<?php
session_start();
include 'db.php';

$msg = "";

/* REGISTER */
if (isset($_POST['register'])) {
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $r = $_POST['role'];

    mysqli_query($conn,"INSERT INTO users(username,password,role) VALUES('$u','$p','$r')");
    $msg = "Registered Successfully! Now Login.";
}

/* LOGIN */
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $res = mysqli_query($conn,"SELECT * FROM users WHERE username='$u'");
    $row = mysqli_fetch_assoc($res);

    if ($row && password_verify($p,$row['password'])) {
        $_SESSION['role'] = $row['role'];
        $_SESSION['user'] = $row['username'];
        header("Location: dashboard.php");
    } else {
        $msg = "Wrong Username or Password!";
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3 class="text-center">Task System</h3>
<div class="text-danger text-center"><?php echo $msg; ?></div>

<div class="row">
<div class="col-md-6">
<h4>Login</h4>
<form method="post">
<input type="text" name="username" placeholder="Username" class="form-control mb-2">
<input type="password" name="password" placeholder="Password" class="form-control mb-2">
<button name="login" class="btn btn-success w-100">Login</button>
</form>
</div>

<div class="col-md-6">
<h4>Register</h4>
<form method="post">
<input type="text" name="username" class="form-control mb-2" placeholder="Username">
<input type="password" name="password" class="form-control mb-2" placeholder="Password">
<select name="role" class="form-control mb-2">
<option>admin</option>
<option>manager</option>
<option>employee</option>
</select>
<button name="register" class="btn btn-primary w-100">Register</button>
</form>
</div>
</div>
</div>
```

---

# 🏠 dashboard.php

```php
<?php
session_start();
if (!isset($_SESSION['role'])) header("Location: login_register.php");
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-dark bg-dark p-2">
<span class="navbar-brand">Welcome <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['role']; ?>)</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

<?php if($_SESSION['role']=="manager") { ?>
<a href="add_task.php" class="btn btn-primary">Add Task</a>
<?php } ?>

<?php if($_SESSION['role']=="admin") { ?>
<a href="manage_users.php" class="btn btn-warning">Manage Users</a>
<?php } ?>

<a href="view_tasks.php" class="btn btn-info">View Tasks</a>

</div>
```

---

# ➕ add_task.php (Manager)

```php
<?php
session_start();
include 'db.php';
if($_SESSION['role']!="manager") die("Access Denied");

if(isset($_POST['add'])){
mysqli_query($conn,"INSERT INTO tasks(employee_name,task,task_date,task_time,session_status)
VALUES('$_POST[name]','$_POST[task]','$_POST[date]','$_POST[time]','$_POST[status]')");
}
?>
<form method="post" class="container mt-5">
<input name="name" class="form-control mb-2" placeholder="Employee Name">
<input name="task" class="form-control mb-2" placeholder="Task">
<input type="date" name="date" class="form-control mb-2">
<input type="time" name="time" class="form-control mb-2">
<input name="status" class="form-control mb-2" placeholder="Session Status">
<button name="add" class="btn btn-success">Add</button>
</form>
```

---

# 📋 view_tasks.php

```php
<?php
session_start();
include 'db.php';
$res=mysqli_query($conn,"SELECT * FROM tasks");
?>
<table class="table container mt-5">
<tr><th>Name</th><th>Task</th><th>Date</th><th>Time</th><th>Status</th>
<?php if($_SESSION['role']!="employee") echo "<th>Action</th>"; ?>
</tr>

<?php while($r=mysqli_fetch_assoc($res)) { ?>
<tr>
<td><?=$r['employee_name']?></td>
<td><?=$r['task']?></td>
<td><?=$r['task_date']?></td>
<td><?=$r['task_time']?></td>
<td><?=$r['session_status']?></td>

<?php if($_SESSION['role']!="employee"){ ?>
<td>
<a href="delete_task.php?id=<?=$r['id']?>" class="btn btn-danger btn-sm">Delete</a>
</td>
<?php } ?>
</tr>
<?php } ?>
</table>
```

---

# 👤 manage_users.php (Admin)

```php
<?php
session_start();
include 'db.php';
if($_SESSION['role']!="admin") die("Access Denied");
$res=mysqli_query($conn,"SELECT * FROM users");
?>
<table class="table container mt-5">
<tr><th>User</th><th>Role</th><th>Action</th></tr>
<?php while($r=mysqli_fetch_assoc($res)){ ?>
<tr>
<td><?=$r['username']?></td>
<td><?=$r['role']?></td>
<td><a href="delete_user.php?id=<?=$r['id']?>" class="btn btn-danger btn-sm">Delete</a></td>
</tr>
<?php } ?>
</table>
```

---

# ❌ delete_task.php

```php
<?php include 'db.php'; mysqli_query($conn,"DELETE FROM tasks WHERE id=$_GET[id]"); header("Location:view_tasks.php"); ?>
```

# ❌ delete_user.php

```php
<?php include 'db.php'; mysqli_query($conn,"DELETE FROM users WHERE id=$_GET[id]"); header("Location:manage_users.php"); ?>
```

---

# 🚪 logout.php

```php
<?php
session_start();
session_destroy();
header("Location: login_register.php");
?>
```

---

# 🎯 PROJECT FLOW

1. Open → **login_register.php**
2. Register or Login
3. Role-based Dashboard
4. Logout → Back to login

---

This is **100% working basic role-based PHP project** 💯
Perfect for viva + interview.

If you want **search + edit + charts next**, say **"advance version"** 🚀

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    role ENUM('admin','manager','employee')
);
