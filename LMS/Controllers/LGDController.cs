using LMS.Models;
using OfficeOpenXml;
using System;
using System.Collections.Generic;
using System.Configuration;
using System.IO;
using System.Linq;
using System.Text.RegularExpressions;
using System.Web;
using System.Web.Mvc;

namespace LMS.Controllers
{
    [WebAuthorization]

    public class LGDController : Controller
    {

        ifrsEntities db = new ifrsEntities();

        // GET: LGD
        /// <summary>
        /// Return Partial View of LGD
        /// </summary>
        /// <returns> LGD View</returns>
        public ActionResult Index()
        {
            return PartialView("LGD");
        }
        /// <summary>
        /// Delete Previous record of LGD data calculation if exist
        /// </summary>
        public void truncateLGDFile()
        {
            string useriD = Session["UserEmail"].ToString();
            //ConsolidatedLGDCalculateds
            var consol = db.ConsolidatedLGDCalculateds.Where(x => x.by_user == useriD).ToList();
            if (consol.Count != 0)
            {
                db.ConsolidatedLGDCalculateds.RemoveRange(consol);
            }
           
            //OutputLGDs
            var ol = db.OutputLGDs.Where(x => x.by_user == useriD).ToList();
            if (ol.Count != 0)
            {
                db.OutputLGDs.RemoveRange(ol);
            }
            //RecoveryOutputLGDs
            var recov = db.RecoveryOutputLGDs.Where(x => x.by_user == useriD).ToList();
            if (recov.Count != 0)
            {
                db.RecoveryOutputLGDs.RemoveRange(recov);
            }
            //LGD_Output
            var lgdoutput = db.LGD_Output.Where(x => x.by_user == useriD).ToList();
            if (lgdoutput.Count != 0)
            {
                db.LGD_Output.RemoveRange(lgdoutput);
            }
            //CostOutputs
            var cost = db.CostOutputs.Where(x => x.by_user == useriD).ToList();
            if (cost.Count != 0)
            {
                db.CostOutputs.RemoveRange(cost);
            }
            //LGD_CostInput
            var lgdcost = db.LGD_CostInput.Where(x => x.by_user == useriD).ToList();
            if (lgdcost.Count != 0)
            {
                db.LGD_CostInput.RemoveRange(lgdcost);
            }
            //LGD_RecoveryInput
            var lgdrecovery = db.LGD_RecoveryInput.Where(x => x.by_user == useriD).ToList();
            if (lgdrecovery.Count != 0)
            {
                db.LGD_RecoveryInput.RemoveRange(lgdrecovery);
            }
            //LGD_General_Input
            var LGI = db.LGD_General_Input.Where(x => x.by_user == useriD).ToList();
            if (LGI.Count != 0)
            {
                db.LGD_General_Input.RemoveRange(LGI);
            }
           

            db.SaveChanges();

        }
        /// <summary>
        ///  read and validate uploaded file by defining file model
        /// for validation this.DataValidation function calling
        /// </summary>
        /// <param name="fm"></param>
        /// <returns> return JSON response</returns>
        public ActionResult LGDUpload(FileModel fm) {
            string FileName = Path.GetFileNameWithoutExtension(fm.fileName.FileName);
            string FileExtension = Path.GetExtension(fm.fileName.FileName);
            FileName = DateTime.Now.ToString("yyyyMMdd") + "-" + FileName.Trim() + FileExtension;
            string UploadPath = Path.Combine(Server.MapPath(ConfigurationManager.AppSettings["FileUpload"].ToString()), FileName);
            fm.fileName.SaveAs(UploadPath);
            string result = this.DataValidation(UploadPath);
            if (result == "Success")
            {
                System.IO.File.Delete(UploadPath);
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "true",type = "General", flag = "", validationStatus = "false" });
            }
            else if (result == "Error")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "General", flag = "", validationStatus = "true", LGDREquired = "false" });
            }
            else if (result == "LGD Empty")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "General", flag = "", validationStatus = "true", LGDREquired = "true" });
            }
            else
            {
                return Json(new { statusCode = "500", file = UploadPath, isValidFile = "false", type = "General", flag = "", validationStatus = "true" });
            }
        }
        /// <summary>
        /// Read and validate recovery input uploaded  file
        /// </summary>
        /// <param name="fm"></param>
        /// <returns> return json response </returns>
        public ActionResult RecoveryUpload(FileModel fm)
        {
            string FileName = Path.GetFileNameWithoutExtension(fm.fileName.FileName);
            string FileExtension = Path.GetExtension(fm.fileName.FileName);
            FileName = DateTime.Now.ToString("yyyyMMdd") + "-" + FileName.Trim() + FileExtension;
            string UploadPath = Path.Combine(Server.MapPath(ConfigurationManager.AppSettings["FileUpload"].ToString()), FileName);
            fm.fileName.SaveAs(UploadPath);
            string result = this.RecoveryDataValidation(UploadPath);
            if (result == "Success")
            {
                //return RedirectToAction("ECL_Calculator");
                System.IO.File.Delete(UploadPath);
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "true", type = "Recovery", flag = "", validationStatus = "false" });
            }
            else if (result == "Error")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "Recovery", flag = "",validationStatus = "true", LGDREquired = "false" });
            }
            else if (result == "LGD Empty")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "Recovery", flag = "",validationStatus = "true", LGDREquired = "true" });
            }
            else
            {
                return Json(new { statusCode = "500", file = UploadPath, isValidFile = "false", type = "Recovery", flag = "", validationStatus = "true" });
            }
        }
        /// <summary>
        /// Read and validate Cost input uploaded  file
        /// </summary>
        /// <param name="fm"></param>
        /// <returns> return json response </returns>
        public ActionResult CostUpload(FileModel fm)
        {
            string FileName = Path.GetFileNameWithoutExtension(fm.fileName.FileName);
            string FileExtension = Path.GetExtension(fm.fileName.FileName);
            FileName = DateTime.Now.ToString("yyyyMMdd") + "-" + FileName.Trim() + FileExtension;
            string UploadPath = Path.Combine(Server.MapPath(ConfigurationManager.AppSettings["FileUpload"].ToString()), FileName);
            fm.fileName.SaveAs(UploadPath);
            string result = this.CostValidate(UploadPath);
            if (result == "Success")
            {
                //return RedirectToAction("ECL_Calculator");
                System.IO.File.Delete(UploadPath);
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "true", type = "Cost", flag = "", validationStatus = "false" });
            }
            else if (result == "Error")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "Cost", flag = "", validationStatus = "true", LGDREquired = "false" });
            }
            else if (result == "LGD Empty")
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", type = "Cost", flag = "", validationStatus = "true", LGDREquired = "true" });
            }
            else
            {
                return Json(new { statusCode = "500", file = UploadPath, isValidFile = "false", type = "Cost", flag = "", validationStatus = "true" });
            }
        }
        List<myError> ErrorList = new List<myError>();
        Dictionary<string, List<string>> portcheckValidate = new Dictionary<string, List<string>>();
        List<string> ratedport = new List<string>() { "SME", "Corporate", "Agri", "Commercial" };
        List<string> nonRated = new List<string>() { "Car Ijarah", "Bike Ijarah", "Commercial Vehicle", "Consumer Ease", "Housing Finance", "Labaik Financing", "Labbaik" };
        List<string> isRestructured = new List<string>() { "AFS", "HTM", "Placements" };
        /// <summary>
        ///  Get Column index by defining Column name and file worksheet
        /// </summary>
        /// <param name="ws">File worksheet where to find column</param>
        /// <param name="columnNames"> Name of column to find</param>
        /// <returns> index of columns</returns>
        /// <exception cref="ArgumentNullException"></exception>
        int GetColumnByName(ExcelWorksheet ws, string columnNames)
        {
            var value = ws.Cells["1:1"].FirstOrDefault(x => x.Text == columnNames.Trim());
            if (value == null)
            {
                string ColumnName = columnNames;
                myError me = new myError();
                me.SerialNumber = 1;
                me.ErrorMessage = "Column " + columnNames + " not Defined";
                me.ErrorInRow = "Column not Defined in first row";
                me.ColumnName = ColumnName;
                ErrorList.Add(me);
                return 0;
            }
            return ws.Cells["1:1"].FirstOrDefault(c => c.Text == columnNames.Trim()).Start.Column;

        }
        /// <summary>
        /// Validate data of General LGD data by defining file name
        /// </summary>
        /// <param name="filename">Name of file to validate</param>
        /// <returns> respnse that data is validate or not by success or error response </returns>
        public string DataValidation(string filename)
        {
            string userID = Session["UserEmail"].ToString();
            var LGII = db.LGD_General_Input.Where(x => x.by_user == userID).ToList();
            if (LGII.Count != 0)
            {
                db.LGD_General_Input.RemoveRange(LGII);
            }
            portcheckValidate.Add("rated", ratedport);
            portcheckValidate.Add("nonrated", nonRated);
            portcheckValidate.Add("Restrucured", isRestructured);
            using (var p = new ExcelPackage())
            {
                FileInfo fi = new FileInfo(filename);
                var package = new ExcelPackage(fi);
                ExcelWorksheet sheet = package.Workbook.Worksheets[0];
                var start = sheet.Dimension.Start;
                var end = sheet.Dimension.End;
                var col_start = sheet.Dimension.Columns;
              //  System.Array myArray = null;
                string[] lines = { "First line", "Second line", "Third line" };

                // Template Validation
                if (String.IsNullOrEmpty(sheet.Cells[1, 1].Text))
                {
                    return "Invalid";
                }
                string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
                List<ECL_GeneralInput> bulk_data = new List<ECL_GeneralInput>();
                int facility_Id = this.GetColumnByName(sheet, "Facility ID");
                int customer_id = this.GetColumnByName(sheet, "Cust id");
                int account_number = this.GetColumnByName(sheet, "Account Number");
                int npa_date = this.GetColumnByName(sheet, "NPA date");
                int balance = this.GetColumnByName(sheet, "Balance");
                int InterestRate = this.GetColumnByName(sheet, "Interest Rate");
                int segments = this.GetColumnByName(sheet, "Segment");
                if (ErrorList.Count != 0)
                {
                    Session["Errors"] = ErrorList;
                    return "Error";
                }
                int col = 0;
                List<string> facilCheck = new List<string>();
                List<LGD_General_Input> LGI = new List<LGD_General_Input>();
                LGI.Clear();
                

                  //  List<string> ErrorList = new List<string>();
                    for (int row = start.Row; row <= end.Row; row++)
                    {
                        // Header Validation
                        //if (row == 1)
                        //{
                        //    myArray = new string[col_start];
                        //    for (int j = 0; j < col_start; j++)
                        //    {
                        //        myArray.SetValue(sheet.Cells[row, (j + 1)].Value.ToString().Replace(" ", ""), j);
                        //    }
                        //}
                        // Required Field Validation
                        if (row >= 2)
                        {
                            LGD_General_Input LGDdata = new LGD_General_Input();
                            Guid g = Guid.NewGuid();
                            col = facility_Id;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                if (facilCheck.Contains(sheet.Cells[row, col].Value.ToString()))
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Facility Id must be Unique";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    LGDdata.FacilityID = sheet.Cells[row, col].Value.ToString();
                                }

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Facility Id Cannot be null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }

                           // LGDdata.FacilityID = sheet.Cells[row, col].Value.ToString();
                            col = customer_id;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                LGDdata.CustomerID = sheet.Cells[row, col].Value.ToString();

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Customer Id Cannot be null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = account_number;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                LGDdata.AccountNO = sheet.Cells[row, col].Value.ToString();
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Account Number Cannot be null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = npa_date;
                            try
                            {
                                LGDdata.NPADate = Convert.ToDateTime(sheet.Cells[row, col].Value.ToString());

                            }
                            catch
                            {
                                try
                                {
                                    long dateNum = long.Parse(sheet.Cells[row, col].Value.ToString());
                                    DateTime dateresult = DateTime.FromOADate(dateNum);
                                    LGDdata.NPADate = dateresult;

                                }
                                catch
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "NPA Date  Cannot be null or must be Date Format";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }

                            col = balance;
                            var ba = sheet.Cells[row, col].Value;
                            double balOut;
                            if (ba != null)
                            {
                                if (double.TryParse(sheet.Cells[row, col].Value.ToString(), out balOut))
                                {
                                    if (balOut>=0)
                                    {
                                        LGDdata.Balance = balOut;
                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Balance  must  be  positive ";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Balance  must  be  Numeric";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Balance  Cannot be Null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                           // LGDdata.Balance = Convert.ToDouble(sheet.Cells[row, 5].Value.ToString());
                            col = InterestRate;
                            double InterstOUt;
                            if (sheet.Cells[row, col].Value != null)
                            {
                                if (double.TryParse(sheet.Cells[row, col].Value.ToString(),out InterstOUt))
                                {
                                    LGDdata.InterestRate = InterstOUt;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Interest Rate must be Numeric";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Interest Rate  Cannot be Null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = segments;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                //if (portcheckValidate["rated"].Contains(sheet.Cells[row, col].Value.ToString())|| portcheckValidate["nonrated"].Contains(sheet.Cells[row, col].Value.ToString())|| portcheckValidate["Restrucured"].Contains(sheet.Cells[row, col].Value.ToString()))
                                //{
                                    LGDdata.Segment = sheet.Cells[row, col].Value.ToString();
                                //}
                                //else
                                //{
                                //    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                //    myError me = new myError();
                                //    me.SerialNumber = row;
                                //    me.ErrorMessage = "Segment '"+ sheet.Cells[row, col].Value.ToString() + "' not Initialize";
                                //    me.ErrorInRow = sheet.Cells[row, col].Address;
                                //    me.ColumnName = ColumnName;
                                //    ErrorList.Add(me);
                                //}
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Segment  Cannot be Null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                        }
                        LGDdata.by_user = Session["UserEmail"].ToString();
                           // LGDdata.Segment = sheet.Cells[row, col].Value.ToString();
                           LGI.Add(LGDdata);
                        }
                    }
               var portlist= db.ECL_GeneralInput.Where(x=>x.LGDRate==null && x.by_user == userID).Select(x => x.PortFolioCode).Distinct().ToList();
              var lgport=  LGI.Select(x => x.Segment).Distinct().ToList();
                foreach (var item in portlist)
                {
                    if (!lgport.Contains(item))
                    {
                        string ColumnName = item;
                        myError me = new myError();
                        me.SerialNumber = 0;
                        me.ErrorMessage = item+"  not found";
                        me.ErrorInRow = "not found";
                        me.ColumnName = ColumnName;
                        ErrorList.Add(me);
                    }
                }
                Session["Errors"] = ErrorList;
                if (ErrorList.Count == 0)
                {
                    db.LGD_General_Input.AddRange(LGI);
                    db.SaveChanges();
                    return "Success";
                }
                else
                {
                    return "Error";
                }
            }
        }
        /// <summary>
        /// Validate data of Recovery Input LGD data by defining file name
        /// </summary>
        /// <param name="filename">Name of file to validate</param>
        /// <returns> respnse that data is validate or not by success or error response </returns>
        public string RecoveryDataValidation(string filename)
        {
            string userId = Session["UserEmail"].ToString();

            var lgdrecovery = db.LGD_RecoveryInput.Where(x => x.by_user == userId).ToList();
            if (lgdrecovery.Count != 0)
            {
                db.LGD_RecoveryInput.RemoveRange(lgdrecovery);
            }
            if (db.LGD_General_Input.Where(x => x.by_user == userId).ToList().Count==0)
            {
                string ColumnName = "LGD Genereral File not Uploaded";
                myError me = new myError();
                me.SerialNumber = 0;
                me.ErrorMessage = "LGD General File not uploaded please upload it for Validations";
                me.ErrorInRow = "0";
                me.ColumnName = ColumnName;
                ErrorList.Add(me);
                Session["Errors"] = ErrorList;

                return "Error";
            }
            ErrorList.Clear();

            using (var p = new ExcelPackage())
            {
                var LGI = db.LGD_General_Input.Where(x=>x.by_user==userId).ToList();
                FileInfo fi = new FileInfo(filename);
                var package = new ExcelPackage(fi);
                ExcelWorksheet sheet = package.Workbook.Worksheets[0];
                var start = sheet.Dimension.Start;
                var end = sheet.Dimension.End;
                var col_start = sheet.Dimension.Columns;
                System.Array myArray = null;
                int facility_Id = this.GetColumnByName(sheet, "Facility ID");
                int account_number = this.GetColumnByName(sheet, "Account No");
                int month_of_recovery = this.GetColumnByName(sheet, "Month of Recovery");
                int recovery_ammount = this.GetColumnByName(sheet, "Recovery Amount");
                if (ErrorList.Count != 0)
                {
                    Session["Errors"] = ErrorList;
                    return "Error";
                }
                string[] lines = { "First line", "Second line", "Third line" };
                List<LGD_RecoveryInput> LRI = new List<LGD_RecoveryInput>();

                int col = 0;
                var facilcheck = db.LGD_General_Input.Where(x=>x.by_user == userId).Select(x => x.FacilityID ).ToList();
                // Template Validation
                if (String.IsNullOrEmpty(sheet.Cells[1, 1].Text))
                {
                    return "Invalid";
                }
                string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
                List<ECL_GeneralInput> bulk_data = new List<ECL_GeneralInput>();

                    for (int row = start.Row; row <= end.Row; row++)
                    {
                        // Header Validation
                        if (row == 1)
                        {
                            myArray = new string[col_start];
                            for (int j = 0; j < col_start; j++)
                            {
                                myArray.SetValue(sheet.Cells[row, (j + 1)].Value.ToString().Replace(" ", ""), j);
                            }
                        }
                        // Required Field Validation
                        if (row >= 2)
                        {
                            LGD_RecoveryInput LGDdata = new LGD_RecoveryInput();
                            Guid g = Guid.NewGuid();
                            //LGDdata. = sheet.Cells[row, 1].Value.ToString();
                            col = facility_Id;
                            if (sheet.Cells[row, col].Value != null)
                            {
                            if (facilcheck.Contains(sheet.Cells[row, col].Value.ToString()))
                            {
                                LGDdata.FacilityID = sheet.Cells[row, col].Value.ToString();
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Facility Id "+ sheet.Cells[row, col].Value.ToString() + " must be in General Input";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }

                    }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Facility Id Cannot be null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = month_of_recovery;
                            try
                            {
                                var d = Convert.ToDateTime(sheet.Cells[row, col].Value.ToString());
                                var chck=db.LGD_General_Input.Where(x => x.FacilityID == LGDdata.FacilityID && x.by_user == userId).FirstOrDefault();
                            if (chck != null)
                            {
                                //if (d > chck.NPADate)
                                //{
                                    LGDdata.MonthOfRecovery = d;

                                //}
                                //else
                                //{
                                //    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                //    myError me = new myError();
                                //    me.SerialNumber = row;
                                //    me.ErrorMessage = "month of recovery Date  should be Greater than NPA date";
                                //    me.ErrorInRow = sheet.Cells[row, col].Address;
                                //    me.ColumnName = ColumnName;
                                //    ErrorList.Add(me);
                                //}

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Facility Id did not exist ";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }

                        }
                            catch { 
                            try
                            {
                                long dateNum = long.Parse(sheet.Cells[row, 3].Value.ToString());
                                DateTime dateresult = DateTime.FromOADate(dateNum);
                                LGDdata.MonthOfRecovery = dateresult;

                            }
                            catch
                            {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "month of recovery Date  Cannot be null or must be Date Format";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                        }



                            col = recovery_ammount;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                var d = 0.0;
                            if (double.TryParse(sheet.Cells[row, col].Value.ToString(),out d))
                            {
                                if (d >= 0)
                                {
                                    //if (LGDdata.FacilityID == "141541662236")
                                    //{
                                    //    int bjreak = 0;
                                    //}

                                    //var bal = LGI.Where(x => x.FacilityID == LGDdata.FacilityID).Sum(x => x.Balance);
                                    //var sum = LRI.Where(x => x.FacilityID == LGDdata.FacilityID).Sum(x => x.RcoveryAmount) + d;
                                    //if (Convert.ToInt32(sum) > (bal + 1))
                                    //{
                                    //    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    //    myError me = new myError();
                                    //    me.SerialNumber = row;
                                    //    me.ErrorMessage = "Recovery Ammount of " + LGDdata.FacilityID + " Cannot be Greater than Bal";
                                    //    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    //    me.ColumnName = ColumnName;
                                    //    ErrorList.Add(me);
                                    //}
                                    //else
                                    //{
                                        LGDdata.RcoveryAmount = d;
                                    //}
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Recovery Ammount Cannot be negative";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                              
                           
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Recovery Ammount Cannot be null or must be Date Format";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                        LGDdata.by_user =Session["UserEmail"].ToString();
                          //  LGDdata.RcoveryAmount = Convert.ToDouble(sheet.Cells[row, col].Value.ToString());
                            LRI.Add(LGDdata);
                        }
                    
                   // outputFile.Close();
                }
                if (ErrorList.Count== 0)
                {
                    db.LGD_RecoveryInput.AddRange(LRI);
                    db.SaveChanges();
                    return "Success";
                }
                else
                {
                    Session["Errors"] = ErrorList;
                    return "Error";
                }
            }
        }

        /// <summary>
        /// formula 
        /// </summary>
        /// <param name="startDate"></param>
        /// <param name="endDate"></param>
        /// <returns></returns>
        public double YearFrac(DateTime startDate, DateTime endDate)
        {
          
            int endDay = endDate.Day;
            int startDay = startDate.Day;
            switch (startDay)
            {
                case 31:
                    {
                        startDay = 30;
                        if (endDay == 31)
                        {
                            endDay = 30;
                        }
                    }
                    break;
                case 30:
                    {
                        if (endDay == 31)
                        {
                            endDay = 30;
                        }
                    }
                    break;
                case 29:
                    {
                        if (startDate.Month == 2)
                        {
                            startDay = 30;
                            if ((endDate.Month == 2) && (endDate.Day == 28 + (DateTime.IsLeapYear(endDate.Year) ? 1 : 0)))
                            {
                                endDay = 30;
                            }
                        }
                    }
                    break;
                case 28:
                    {
                        if ((startDate.Month == 2) && (!DateTime.IsLeapYear(startDate.Year)))
                        {
                            startDay = 30;
                            if ((endDate.Month == 2) && (endDate.Day == 28 + (DateTime.IsLeapYear(endDate.Year) ? 1 : 0)))
                            {
                                endDay = 30;
                            }
                        }
                    }
                    break;
            }
            return ((endDate.Year - startDate.Year) * 360 + (endDate.Month - startDate.Month) * 30 + (endDay - startDay)) / 360.0;
        }
        /// <summary>
        /// Validate data of Cost Input LGD data by defining file name
        /// </summary>
        /// <param name="filename"></param>
        /// <returns></returns>
        public string CostValidate(string filename) {

            string userId = Session["UserEmail"].ToString();
            //CostOutputs
            var costtt = db.CostOutputs.Where(x => x.by_user == userId).ToList();
            if (costtt.Count != 0)
            {
                db.CostOutputs.RemoveRange(costtt);
            }
            if (db.LGD_General_Input.Where(x => x.by_user == userId).ToList().Count == 0)
            {
                string ColumnName = "LGD Genereral File not Uploaded";
                myError me = new myError();
                me.SerialNumber = 0;
                me.ErrorMessage = "LGD General File not uploaded please upload it for Validations";
                me.ErrorInRow = "0";
                me.ColumnName = ColumnName;
                ErrorList.Add(me);
                Session["Errors"] = ErrorList;

                return "Error";
            }
            ErrorList.Clear();
            using (var p = new ExcelPackage())
            {
                FileInfo fi = new FileInfo(filename);
                var package = new ExcelPackage(fi);
                ExcelWorksheet sheet = package.Workbook.Worksheets[0];
                var start = sheet.Dimension.Start;
                var end = sheet.Dimension.End;
                var col_start = sheet.Dimension.Columns;
                int facility_Id = this.GetColumnByName(sheet, "Facility ID");
                int month_of_recovery = this.GetColumnByName(sheet, "Month of Cost");
                int cost = this.GetColumnByName(sheet, "Cost");
                if (ErrorList.Count != 0)
                {
                    Session["Errors"] = ErrorList;
                    return "Error";
                }
                var facilcheck = db.LGD_General_Input.Where(x=>x.by_user==userId).Select(x => x.FacilityID).ToList();
                List<LGD_CostInput> LCI = new List<LGD_CostInput>();
                int col = 0;
                System.Array myArray = null;
                string[] lines = { "First line", "Second line", "Third line" };

                // Template Validation
                if (String.IsNullOrEmpty(sheet.Cells[1, 1].Text))
                {
                    return "Invalid";
                }
                string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
                List<ECL_GeneralInput> bulk_data = new List<ECL_GeneralInput>();
                //using (StreamWriter outputFile = new StreamWriter(fileName))
                //{
                    for (int row = start.Row; row <= end.Row; row++)
                    {
                        // Header Validation
                        if (row == 1)
                        {
                            myArray = new string[col_start];
                            for (int j = 0; j < col_start; j++)
                            {
                                myArray.SetValue(sheet.Cells[row, (j + 1)].Value.ToString().Replace(" ", ""), j);
                            }
                        }
                        // Required Field Validation
                        if (row >= 2)
                        {
                            LGD_CostInput ci = new LGD_CostInput();
                            Guid g = Guid.NewGuid();
                            
                            //ci.AccountNo = sheet.Cells[row, 1].Value.ToString();
                            col = facility_Id;
                            if (sheet.Cells[row, col].Value != null)
                            {
                                if (facilcheck.Contains(sheet.Cells[row, col].Value.ToString()))
                                {
                                    ci.FacilityID = sheet.Cells[row, col].Value.ToString();
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Facility Id "+ sheet.Cells[row, col].Value.ToString() + " must be in General Input";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Facility Id Cannot be null";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            //ci.MonthOfCost = Convert.ToDateTime(sheet.Cells[row, 3].Value.ToString());
                            col = month_of_recovery;
                            try
                            {
                                var d = Convert.ToDateTime(sheet.Cells[row, col].Value.ToString());
                                var chck = db.LGD_General_Input.Where(x => x.FacilityID == ci.FacilityID && x.by_user == userId).FirstOrDefault();
                                //if (d > chck.NPADate)
                                //{
                                    ci.MonthOfCost = d;

                                //}
                                //else
                                //{
                                //    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                //    myError me = new myError();
                                //    me.SerialNumber = row;
                                //    me.ErrorMessage = "month of recovery Date  should be Greater than NPA date";
                                //    me.ErrorInRow = sheet.Cells[row, col].Address;
                                //    me.ColumnName = ColumnName;
                                //    ErrorList.Add(me);
                                //}

                            }
                            catch
                            {
                                try
                                {
                                    long dateNum = long.Parse(sheet.Cells[row, 3].Value.ToString());
                                    DateTime dateresult = DateTime.FromOADate(dateNum);
                                    ci.MonthOfCost = dateresult;

                                }
                                catch
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "month of recovery Date  Cannot be null or must be Date Format";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            col = cost;

                            if (sheet.Cells[row, col].Value!=null)
                            {
                                if (Convert.ToDouble(sheet.Cells[row, col].Value.ToString())>0)
                                {
                                    ci.CostAmount = Convert.ToDouble(sheet.Cells[row, col].Value.ToString());
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Cost must be Numeric";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Cost Cannot be null ";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            ci.by_user =Session["UserEmail"].ToString();
                            LCI.Add(ci);
                        }
                    }
                //}
                if (ErrorList.Count == 0)
                {
                    db.LGD_CostInput.AddRange(LCI);
                    db.SaveChanges();
                    return "Success";
                }
                else
                {
                    Session["Errors"] = ErrorList;
                    return "Error";
                }
            }


        }
        //[HttpPost]
        /// <summary>
        /// remove previous record
        /// Caculate LGD with methadoligy  after file uploadation and 
        /// </summary>
        /// <returns> LGD output </returns>
        public ActionResult CalculateLGD()
        {
            string userId = Session["UserEmail"].ToString();
            //ConsolidatedLGDCalculateds
            var consol = db.ConsolidatedLGDCalculateds.Where(x => x.by_user == userId).ToList();
            if (consol.Count != 0)
            {
                db.ConsolidatedLGDCalculateds.RemoveRange(consol);
            }

            //OutputLGDs
            var ol = db.OutputLGDs.Where(x => x.by_user == userId).ToList();
            if (ol.Count != 0)
            {
                db.OutputLGDs.RemoveRange(ol);
            }
            //RecoveryOutputLGDs
            var recov = db.RecoveryOutputLGDs.Where(x => x.by_user == userId).ToList();
            if (recov.Count != 0)
            {
                db.RecoveryOutputLGDs.RemoveRange(recov);
            }
            //LGD_Output
            var lgdoutput = db.LGD_Output.Where(x => x.by_user == userId).ToList();
            if (lgdoutput.Count != 0)
            {
                db.LGD_Output.RemoveRange(lgdoutput);
            }
            //CostOutputs
            var cost = db.CostOutputs.Where(x => x.by_user == userId).ToList();
            if (cost.Count != 0)
            {
                db.CostOutputs.RemoveRange(cost);
            }
            var lgdInputData = db.LGD_General_Input.Where(x => x.by_user == userId).ToList();
            var lgdRecoveryInputData = db.LGD_RecoveryInput.Where(x => x.by_user == userId).ToList();
            var lgdCostInputData = db.LGD_CostInput.Where(x => x.by_user == userId).ToList();
            double yearfrac;
            foreach (var li in lgdRecoveryInputData)
            {
                RecoveryOutputLGD rol = new RecoveryOutputLGD();
                rol.AccountNO = li.FacilityID;
                rol.MonthOfRecovery = li.MonthOfRecovery;
                rol.TranAmount = li.RcoveryAmount;
                rol.NPA_Date = lgdInputData.Where(x => x.FacilityID.ToString() == li.FacilityID.ToString()).FirstOrDefault().NPADate;
                rol.InterestRate = (double)lgdInputData.Where(x => x.FacilityID.ToString() == li.FacilityID.ToString())
                    .Select(y => y.InterestRate).FirstOrDefault();
                if (rol.NPA_Date > rol.MonthOfRecovery)
                {
                    yearfrac = this.YearFrac(rol.MonthOfRecovery, rol.NPA_Date);
                }
                else
                {
                    yearfrac = this.YearFrac(rol.NPA_Date, rol.MonthOfRecovery);
                }
                double recv_eco = ((1 + rol.InterestRate));
                rol.EconomicRecovery = (rol.TranAmount / Math.Pow(recv_eco, yearfrac));
                rol.by_user =Session["UserEmail"].ToString();
                db.RecoveryOutputLGDs.Add(rol);
            }
            foreach (var li in lgdCostInputData)
            {
                yearfrac = 0;
                CostOutput co = new CostOutput();
                co.FacilityID = li.FacilityID;
                co.MonthOfCost = li.MonthOfCost;
                co.CostAmount = li.CostAmount;
                co.NPA_Date = lgdInputData.Where(x => x.FacilityID.ToString() == li.FacilityID.ToString()).FirstOrDefault().NPADate;
                co.InterestRate = (double)lgdInputData.Where(x => x.FacilityID.ToString() == li.FacilityID.ToString())
                    .Select(y => y.InterestRate).FirstOrDefault();
                if (co.NPA_Date > co.MonthOfCost)
                {
                    yearfrac = this.YearFrac(co.MonthOfCost, co.NPA_Date.Value);
                }
                else
                {
                    yearfrac = this.YearFrac(co.NPA_Date.Value, co.MonthOfCost);
                }
                double cost_eco = ((1 + co.InterestRate.Value));
                co.EconomicCost = (co.CostAmount / Math.Pow(cost_eco, yearfrac));
                co.by_user =Session["UserEmail"].ToString();


                db.CostOutputs.Add(co);
            }


            db.SaveChanges();

            List<LGD_Output> lgdout = new List<LGD_Output>();
            foreach (var li in lgdInputData)
            {

                LGD_Output lo = new LGD_Output();
                lo.AccountNo = li.AccountNO;
                lo.Segment = li.FacilityID;
                lo.IndustrySegment = li.Segment;
                lo.NPADate = li.NPADate;
                lo.Balance = li.Balance;
                var bb = db.LGD_RecoveryInput.Where(rr => rr.FacilityID == li.FacilityID && rr.by_user == userId).ToList();
                var d = bb.Where(x => x.FacilityID == li.FacilityID).ToList();

                if (d.Count!=0)
                {
                    lo.HistoricalRecovery = d.Sum(x => x.RcoveryAmount);
                }
                else
                {
                    lo.HistoricalRecovery = 0;
                }
                
                
                var eR = db.RecoveryOutputLGDs.Where(x => x.AccountNO == li.AccountNO && x.by_user == userId).ToList();
                eR = eR.Where(x => x.AccountNO == li.AccountNO).ToList();
                if (eR.Count!=0)
                {
                    lo.EconomicRecovery = eR.Sum(x => x.EconomicRecovery);
                }
                else
                {
                    lo.EconomicRecovery = 0;
                }
                var hC = db.LGD_CostInput.Where(rr => rr.FacilityID == li.FacilityID && rr.by_user == userId).ToList();
                hC = hC.Where(x => x.FacilityID == li.FacilityID).ToList();
                if (hC.Count!=0)
                {
                    lo.HistoricalCost = hC.Sum(x => x.CostAmount);
                }
                else
                {
                    lo.HistoricalCost = 0;
                }
                var eCs = db.CostOutputs.Where(rr => rr.FacilityID.ToString() == li.FacilityID.ToString() && rr.by_user == userId).ToList();
                eCs = eCs.Where(rr => rr.FacilityID == li.FacilityID).ToList();
                if (eCs.Count!=0)
                {
                    lo.EconomicCost = eCs.Sum(x=>x.EconomicCost.Value);
                }
                else
                {
                    lo.EconomicCost = 0;
                }
                if (lo.HistoricalRecovery>=lo.Balance)
                {
                    lo.HistoricalRecovery = lo.Balance;
                }
                if (lo.EconomicRecovery >= lo.Balance)
                {
                    lo.EconomicRecovery = lo.Balance;
                }
              
                if (lo.Balance==0)
                {
                    lo.EconomicLGD = 0;
                    lo.HistoricalLGD = 0;
                }
                else
                {
                    if (lo.EconomicCost >= lo.EconomicRecovery)
                    {
                        lo.EconomicLGD = 1;
                    }
                    else
                    {
                        lo.EconomicLGD = (1 - (lo.EconomicRecovery - lo.EconomicCost) / lo.Balance);

                    }
                    if (lo.HistoricalCost>=lo.HistoricalRecovery)
                    {
                        lo.HistoricalLGD =1;

                    }
                    else
                    {
                        lo.HistoricalLGD = (1 - (lo.HistoricalRecovery - lo.HistoricalCost) / li.Balance);

                    }

                }
                lo.by_user =Session["UserEmail"].ToString();
                lgdout.Add(lo);
               
            }
            db.LGD_Output.AddRange(lgdout);
            db.SaveChanges();

            ConsolidatedLGDCalculated clgd = new ConsolidatedLGDCalculated();
            return Json(new { statusCode = "200", file = "", isValidFile = "true", flag = "", status = "true" });
        }
        /// <summary>
        /// return the output after calculation 
        /// </summary>
        /// <returns></returns>
        public ActionResult ConsolidatedLGD() {
            string userId = Session["UserEmail"].ToString();
            var result = db.ConsolidatedLGDCalculateds.Where(x => x.by_user == userId).ToList();
            if (result.Count == 0)
            {
                var GeneralCalculationLGD = db.LGD_Output.Where(x => x.by_user == userId).GroupBy(x => x.IndustrySegment).Select(y => new
                {
                    Segment = y.Min(z => z.IndustrySegment),
                    AcccountsCount = y.Count(z => z.AccountNo != null),
                    ExposureAtDefualt = y.Sum(z => z.Balance),
                    Recovries = y.Sum(z => z.HistoricalRecovery),
                    Cost = y.Sum(z => z.HistoricalCost),
                    EconomicRecovery = y.Sum(z => z.EconomicRecovery),
                    EconomicCost = y.Sum(z => z.EconomicCost)
                }).ToList();
                foreach (var li in GeneralCalculationLGD)
                {
                    ConsolidatedLGDCalculated clgd = new ConsolidatedLGDCalculated();
                    clgd.Segment = li.Segment;
                    clgd.CountOfAccount = li.AcccountsCount;
                    clgd.ExposureAtDefault = li.ExposureAtDefualt;
                    clgd.Recoveries = li.Recovries;
                    clgd.Costs = li.Cost;
                    clgd.EconomicRecoveries = li.EconomicRecovery;
                    clgd.EconomicCosts = li.EconomicCost;
                    clgd.RecoveryPercent = ((clgd.Recoveries - clgd.Costs) / clgd.ExposureAtDefault);
                    clgd.EconomicRecoveryPercent = ((clgd.EconomicRecoveries - clgd.EconomicCosts) / clgd.ExposureAtDefault);
                    clgd.LGD = (1 - clgd.RecoveryPercent);
                    clgd.EconomicLGD = (1 - clgd.EconomicRecoveryPercent);
                    clgd.by_user =Session["UserEmail"].ToString();
                    db.ConsolidatedLGDCalculateds.Add(clgd);
                }
                db.SaveChanges();
            }
            var resultlist = db.ConsolidatedLGDCalculateds.Where(x=>x.by_user==userId).ToList();
            return PartialView("ShowLGD",resultlist);        
        }    
        /// <summary>
        /// export Errors of validation in excel file
        /// </summary>
        public void ExportErrors()
        {
            ExcelPackage pck = new ExcelPackage();
            var ws = pck.Workbook.Worksheets.Add("Error Log File");
            ws.Cells["A1"].Value = "Serial Number";
            ws.Cells["B1"].Value = "Column Name";
            ws.Cells["C1"].Value = "Count";
            ws.Cells["D1"].Value = "Error Message";
            ws.Cells["E1"].Value = " Error in Rows";
            ws.Cells["A1:Z1"].Style.Font.Bold = true;
            ws.Cells["A1:W1"].AutoFilter = true;
            ws.Cells["A1:W1"].AutoFitColumns();
            int row = 2;
            var er = Session["Errors"] as List<myError>;
            var errors = (from tb in er
                          select new { tb.ColumnName, tb.ErrorInRow, tb.ErrorMessage } into x
                          group x by new { x.ColumnName, x.ErrorMessage }
into g
                          select new
                          {
                              ColumnName = g.Key.ColumnName,
                              Count = g.Select(x => x.ErrorInRow).Count(),
                              ErrorMessage = g.Key.ErrorMessage,
                              ErrorInRow = string.Join(",", g.Select(x => x.ErrorInRow)),

                          }).ToList();
            int i = 1;
            foreach (var data in errors)
            {
                ws.Cells["A" + row].Value = i;
                ws.Cells["B" + row].Value = data.ColumnName;
                ws.Cells["C" + row].Value = data.Count;
                ws.Cells["D" + row].Value = data.ErrorMessage;
                ws.Cells["E" + row].Value = data.ErrorInRow;
                row++;
                i++;
            }
            //  Session["Errors"] = 0;
            Response.Clear();
            Response.ClearHeaders();
            Response.BinaryWrite(pck.GetAsByteArray());
            Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            Response.AddHeader("content-disposition", "attachment;  filename=Error ECL.xlsx");
            Response.End();

        }
        public string GetFileSizeOnDisk()
        {
            string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
            FileInfo fInf = new FileInfo(fileName);
            StreamReader savednames = new System.IO.StreamReader(fileName);
            string sLen = "";
            while (true)
            {
                var line = savednames.ReadLine();
                sLen += line;
                if (line == null)
                {
                    sLen += "";
                    break;
                }
            }
            return sLen;
        }

    }
}
