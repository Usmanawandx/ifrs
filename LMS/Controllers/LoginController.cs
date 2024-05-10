using LMS.Models;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.DirectoryServices;
using System.Text.RegularExpressions;

namespace LMS.Controllers
{
    [WebAuthorization]
    public class LoginController : Controller
    {
        // GET: Login
        //Initiating instance for Entities
        ifrsEntities db = new ifrsEntities();
        //Anotate to Allow guest User
        [AllowAnonymous]
        public ActionResult Index()
        {
            Session.Clear();
            return PartialView("Login");
        }

        [HttpPost]
        public ActionResult LoginUser(user _user)
        {
            Session.Clear();
            string ldapServer = "LDAP://172.30.7.246:389";
            string userName = "ifrs@adtest.com";
            string password = "mbl@1234";
            //LDAP Directory Connection
            var dirctoryEntry = new DirectoryEntry(ldapServer, _user.email + "@adtest.com", _user.password, AuthenticationTypes.ServerBind);
            var dt = DateTime.Now.Date;
            var dtOneHour = DateTime.Now.Minute;
            var nowtime = DateTime.Now.TimeOfDay;
            var dbUserLog = db.User_log.Where(x => x.email == _user.email).ToList();
            try
            {
                //Checking User prevouisly try to login
                var dta = dbUserLog.Where(x => x.email == _user.email&& x.time.Value>=nowtime && (x.time.Value.Minutes ) > dtOneHour&& x.login_time == dt).FirstOrDefault();

                if (dta != null)
                {
                    //if yes then Block 
                    if (dta.login_count == 3)
                    {
                        
                        ViewBag.Error = "Account is blocked";
                        return PartialView("Login");

                    }
                    //code for Connecting Local And Developer
                    if ((_user.email == "ifrs"|| _user.email == "ifrs2"||_user.email == "ifrs3") && _user.password == "mbl@1234")
                        {
                            int u = 00;
                        }
                        else
                        {
                            throw new Exception("user and password is incorrect");
                        }
                    //  for LDAP Accessing
                    // Commented 
                    //object nativeObject = dirctoryEntry.NativeObject;
                }
                //code for Connecting Local And Developer
                if ((_user.email == "ifrs" || _user.email == "ifrs2" || _user.email == "ifrs3") && _user.password == "mbl@1234")
                {
                    int u = 00;
                }
                else
                {
                    throw new Exception("user and password is incorrect");
                }
                //  for LDAP Accessing
                // Commented 
                //object nativeObject = dirctoryEntry.NativeObject;

                //Rest of the logic
            }
            catch (Exception ex)
            {
                var dta = dbUserLog.Where(x => x.email == _user.email && x.time.Value >= nowtime && (x.time.Value.Minutes) > dtOneHour && x.login_time == dt).FirstOrDefault();

                if (dta != null)
                {
                    if (dta.login_count == 1)
                    {
                        dta.login_count = 2;
                    }
                    else if (dta.login_count == null)
                    {
                        dta.login_count = 1;
                    }
                    else if (dta.login_count == 2)
                    {
                        dta.login_count = 3;

                    }
                    else if (dta.login_count == 3)
                    {
                        ViewBag.Error = "Account is blocked";
                        return PartialView("Login");

                    }
                    //updating User logs
                    db.Entry(dta).State = System.Data.Entity.EntityState.Modified;
                    db.SaveChanges();
                }
                else
                {
                    //creating User log
                    User_log ul = new User_log();
                    ul.email = _user.email;
                    ul.login_time = DateTime.Now.Date;
                    ul.login_count = 1;
                    ul.time = DateTime.Now.AddMinutes(5).TimeOfDay;
                    db.User_log.Add(ul);
                    db.SaveChanges();
                }
                ViewBag.Error = ex.Message;
                return PartialView("Login");
            }
            //creating or updating session 
            System.Web.HttpBrowserCapabilities b = System.Web.HttpContext.Current.Request.Browser;
            var us = db.User_sessions.Where(x => x.email == _user.email).FirstOrDefault();
            if (us!=null)
            {
                us.SessionId= System.Web.HttpContext.Current.Session.SessionID;
                us.browser_id = b.Id;
                us.address = System.Web.HttpContext.Current.Request.UserHostAddress;
                db.Entry(us).State = System.Data.Entity.EntityState.Modified;
                db.SaveChanges();
            }
            else
            {

                User_sessions uus = new User_sessions();
                uus.email = _user.email;
                uus.SessionId = System.Web.HttpContext.Current.Session.SessionID;
                uus.browser_id = b.Id;
                uus.address = System.Web.HttpContext.Current.Request.UserHostAddress;
                db.User_sessions.Add(uus);
                db.SaveChanges();
            }
            Session["UserEmail"] = _user.email;

            return RedirectToAction("Index", "Home");
        }

        public ActionResult LogOut()
        {
            Session.Clear();
            Session.Abandon(); // Session Expire but cookie do exist
            Response.Cookies["ASP.NET_SessionId"].Expires = DateTime.Now.AddDays(-30); //Delete the cookie
            return RedirectToAction("Index", "Login");
        }
    }

}