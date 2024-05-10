using LMS.Controllers;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Web;
using System.Web.Mvc;
using System.Web.Optimization;
using System.Web.Routing;

namespace LMS
{
    public class MvcApplication : System.Web.HttpApplication
    {
        protected void Application_Start()
        {
            AreaRegistration.RegisterAllAreas();
            FilterConfig.RegisterGlobalFilters(GlobalFilters.Filters);
            RouteConfig.RegisterRoutes(RouteTable.Routes);
            BundleConfig.RegisterBundles(BundleTable.Bundles);
            ValueProviderFactories.Factories.Add(new JsonValueProviderFactory());
        }
        //protected void Application_BeginRequest(object sender, EventArgs e)
        //{
        //    //Check If it is a new session or not , if not then do the further checks
        //    if (Request.Cookies["ASP.NET_SessionId"] != null && Request.Cookies["ASP.NET_SessionId"].Value != null)
        //    {
        //        string newSessionID = Request.Cookies["ASP.NET_SessionID"].Value;
        //        //Check the valid length of your Generated Session ID
        //        if (newSessionID.Length <= 24)
        //        {
        //            //Log the attack details here
        //            //Response.Cookies["TriedTohack"].Value = "True";
        //            Response.Clear();
        //            Server.ClearError();
        //            var routeData = new RouteData();
        //            routeData.Values["controller"] = "Login";
        //            routeData.Values["action"] = "Index";
        //            Response.StatusCode = 200;
        //            IController controller = new LoginController();
        //            var rc = new RequestContext(new HttpContextWrapper(Context), routeData);
        //            controller.Execute(rc);
        //            throw new HttpException("Invalid Request 1");
        //        }

        //        //Genrate Hash key for this User,Browser and machine and match with the Entered NewSessionID
        //        if (GenerateHashKey() != newSessionID.Substring(24))
        //        {
        //            //Log the attack details here
        //            //Response.Cookies["TriedTohack"].Value = "True";
        //            Response.Clear();
        //            Server.ClearError();
        //            var routeData = new RouteData();
        //            routeData.Values["controller"] = "Login";
        //            routeData.Values["action"] = "Index";
        //            Response.StatusCode = 200;
        //            IController controller = new LoginController();
        //            var rc = new RequestContext(new HttpContextWrapper(Context), routeData);
        //            controller.Execute(rc);
        //            //RedirectToAction("Index", "Login");
        //            //throw new HttpException("Invalid Request");

        //            //Session.Clear();
        //            //Session.Abandon(); // Session Expire but cookie do exist

        //            //Response.Cookies["ASP.NET_SessionId"].Expires = DateTime.Now.AddDays(-30); //Delete the cookie
        //        }

        //        //Use the default one so application will work as usual//ASP.NET_SessionId
        //        Request.Cookies["ASP.NET_SessionId"].Value = Request.Cookies["ASP.NET_SessionId"].Value.Substring(0, 24);
        //    }

        //}



        //protected void Application_EndRequest(object sender, EventArgs e)
        //{
        //    //Pass the custom Session ID to the browser.
        //    if (Response.Cookies["ASP.NET_SessionId"] != null)
        //    {
        //        Response.Cookies["ASP.NET_SessionId"].Value = Request.Cookies["ASP.NET_SessionId"].Value + GenerateHashKey();
        //    }

        //}
        //private string GenerateHashKey()
        //{
        //    StringBuilder myStr = new StringBuilder();
        //    myStr.Append(Request.Browser.Browser);
        //    myStr.Append(Request.Browser.Platform);
        //    myStr.Append(Request.Browser.MajorVersion);
        //    myStr.Append(Request.Browser.MinorVersion);
        //    myStr.Append(Request.UserHostAddress);
        //    //myStr.Append(Request.UserAgent);
        //    //myStr.Append(Request.LogonUserIdentity.User.Value);
        //    SHA1 sha = new SHA1CryptoServiceProvider();
        //    byte[] hashdata = sha.ComputeHash(Encoding.UTF8.GetBytes(myStr.ToString()));
        //    return Convert.ToBase64String(hashdata);
        //}
    }
}
