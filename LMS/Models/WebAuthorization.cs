using System;
using LMS.Models;
using System.Linq;

using System.Collections.Generic;
using System.Web;
using System.Web.Mvc;

namespace LMS.Models
{
    public class WebAuthorization : ActionFilterAttribute
    {
        /// <summary>
        /// Middleware for Authenticate User Session
        /// </summary>
        /// <param name="filterContext"></param>
        public override void OnActionExecuting(ActionExecutingContext filterContext)
        {
            
            var user_id = "";
            ifrsEntities db = new ifrsEntities();

            if (HttpContext.Current.Session["UserEmail"]!=null)
            {
                 user_id = HttpContext.Current.Session["UserEmail"].ToString();
            }
            User_sessions us = db.User_sessions.Where(x=>x.email==user_id).FirstOrDefault();
            var urlHelper = new UrlHelper(filterContext.RequestContext);
            System.Web.HttpBrowserCapabilities b = System.Web.HttpContext.Current.Request.Browser;

            if (HttpContext.Current.Session["UserEmail"] != null && HttpContext.Current.Session.SessionID == us.SessionId && us.address== HttpContext.Current.Request.UserHostAddress&&us.browser_id==b.Id)
            {


                var rd = HttpContext.Current.Request.RequestContext.RouteData;
                string currentController = rd.GetRequiredString("controller");
                var action = filterContext.ActionDescriptor.ActionName;
                if (action == "Index" && currentController == "Login")
                {
                    filterContext.Result = new RedirectResult("~/Home/Index");
                }
                if (HttpContext.Current.Session["UserEmail"] == null)
                {
                    if (!filterContext.HttpContext.Request.IsAjaxRequest())
                        filterContext.Result = new RedirectResult("~/Error/Permission");
                    else
                    {
                        filterContext.HttpContext.Response.StatusCode = 403;
                        filterContext.Result = new JsonResult
                        {
                            Data = new
                            {
                                Error = "NotAuthorized",
                                LogOnUrl = urlHelper.Action("Permission", "Error")
                            },
                            JsonRequestBehavior = JsonRequestBehavior.AllowGet
                        };
                    }
                }
            }
            else
            {
                var rd = HttpContext.Current.Request.RequestContext.RouteData;
                string currentController = rd.GetRequiredString("controller");
                var action = filterContext.ActionDescriptor.ActionName;
                if (!((action == "Index" && currentController == "Login") || (action == "LoginUser" && currentController == "Login")))
                {
                    if (!filterContext.HttpContext.Request.IsAjaxRequest())
                        filterContext.Result = new RedirectResult("~/Login/Index");
                    else
                    {
                        filterContext.HttpContext.Response.StatusCode = 403;
                        filterContext.Result = new JsonResult
                        {
                            Data = new
                            {
                                Error = "SessionExpire",
                                LogOnUrl = urlHelper.Action("Index", "Login")
                            },
                            JsonRequestBehavior = JsonRequestBehavior.AllowGet
                        };
                    }
                }

            }
            base.OnActionExecuting(filterContext);
        }
    }
}