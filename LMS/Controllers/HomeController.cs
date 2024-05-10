using LMS.Models;
using System;
using System.Collections.Generic;
using System.Collections;
using MathNet.Numerics.Distributions;
using System.Configuration;
using System.Data;
using System.Data.OleDb;
using System.IO;
using System.Linq;
using System.Text;
using System.Web;
using System.Web.Mvc;
using OfficeOpenXml;
using System.Globalization;
using OfficeOpenXml.DataValidation;
using System.Management;
using System.Drawing;
using System.Threading.Tasks;
using Microsoft.Ajax.Utilities;
using System.Data.Entity;
using System.Data.SqlClient;
using Newtonsoft.Json;
using System.Threading;
using Microsoft.AspNet.SignalR;
using RealTimeProgressBar;

namespace LMS.Controllers
{
    //  [AllowJsonGet]
    [WebAuthorization]

    public class HomeController : BaseController
    {

        ifrsEntities db = new ifrsEntities();
        //initializing Excel
        public HomeController()
        {
            db = new ifrsEntities();
            ExcelPackage.LicenseContext = LicenseContext.Commercial;

        }
        /// <summary>
        /// return View after login
        /// </summary>
        /// <returns></returns>
        public ActionResult Index()
        {
            empty();
            ErrorList.Clear();
            Facilityoutput.Clear();
            return View();
        }
        /// <summary>
        /// Delete all User data by calling Stored Procedure
        /// </summary>
        public void empty()
        {
            string useriD = Session["UserEmail"].ToString();
            db.emptyTable(useriD);
        }

      /// <summary>
      /// Staging
      /// </summary>
      /// <returns></returns>
        public JsonResult StagingWiseDataBindLGD()
        {
            var data = this.StagingWiseData();
            return Json(new { staging_data = data }, JsonRequestBehavior.AllowGet);
        }
        //set LGD after Calculation
        public ActionResult setLGD()
        {
            string userId = Session["UserEmail"].ToString();
            var lgd_result = db.ConsolidatedLGDCalculateds.Where(x => x.by_user == userId).ToList();
            foreach (var item in lgd_result)
            {
                db.setLGD(item.Segment, item.EconomicLGD, userId);
                
            }
            string response = this.ECL_Calculator();
            if (response == "success")
            {
                var data = this.StagingWiseData();
                return Json(new { statusCode = "200", staging_data = data, file = "", isValidFile = "true", flag = "", validationStatus = "false" });
            }
            else if (response == "PD notRated Empty")
            {
                return Json(new { statusCode = "200",  isValidFile = "false", flag = "nonRated", validationStatus = "true", PDRequired = "true" });
            }
            else if (response == "PD Rated Empty")
            {
                return Json(new { statusCode = "200", isValidFile = "false", flag = "Rated", validationStatus = "true", PDRequired = "true" });
            }
            else if (response == "PD both Empty")
            {
                return Json(new { statusCode = "200",isValidFile = "false", flag = "Empty", validationStatus = "true", PDRequired = "true" });
            }
            else if (response == "PD Empty")
            {
                return Json(new { statusCode = "200", isValidFile = "false", flag = "", validationStatus = "true", PDRequired = "true" });

            }
            else if (response == "LGD Empty")
            {
                return Json(new { statusCode = "200",  isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "true" });

            }
            else
            {
                return Json(new { statusCode = "500", isValidFile = "false", flag = "", validationStatus = "true" });
            }
            //  return PartialView("Index");
        }
        public void runecl()
        {

            FileModel fm = new FileModel();
        }
        //multiple facility upload file
        [HttpPost]
        public async Task<ActionResult> uploadFacilitie(FileModel fm)
        {
            Facilityoutput.Clear();
            Session["Facilityoutput"] = null;
            string UploadPath;
            ProgressHub.SendMessage("initializing and preparing", 2);
            if (fm.fileName == null)
            {
                return Json(new { statusCode = "200", isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "false" });
            }
            string FileName = Path.GetFileNameWithoutExtension(fm.fileName.FileName);
            string FileExtension = Path.GetExtension(fm.fileName.FileName);
            FileName = DateTime.Now.ToString("yyyyMMdd") + "-" + FileName.Trim() + FileExtension;
            UploadPath = Path.Combine(Server.MapPath(ConfigurationManager.AppSettings["FileUpload"].ToString()), FileName);
            fm.fileName.SaveAs(UploadPath);
            Session["file"] = UploadPath;
            ProgressHub.SendMessage("Validating ", 10);
            ValidateDataObject vdo=  await this.DataValidationFacilites(UploadPath);
            if (vdo.response== "Success")
            {
                // return Json(new { statusCode = "200" }, JsonRequestBehavior.AllowGet);
               var ddata = vdo.InsertData;
                Session["Facilityoutput"] = ddata;
                //Facilityoutput.AddRange(ddata);
                return Json(new { statusCode = "200" ,data=ddata, isValidFile ="true"}, JsonRequestBehavior.AllowGet);
                //ProgressHub.SendMessage("Validating success  and data upload successfully ", 25);
                //string response = this.ECL_Calculator();

                //System.IO.File.Delete(UploadPath);

                //if (response == "success")
                //{
                //    ProgressHub.SendMessage("Calculation Done Generating Reports", 80); 
                //    //var data = this.StagingWiseData();
                //    ProgressHub.SendMessage("Completed", 100);
                //    return Json(new { statusCode = "200",file = "", isValidFile = "true", flag = "", validationStatus = "false" }, JsonRequestBehavior.AllowGet);
                //}
            }
            else
            {
                return Json(new { statusCode = "500", file = UploadPath, isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "false" }, JsonRequestBehavior.AllowGet);
            }
        }
        //[HttpPost]
        [System.Web.Services.WebMethod]
        //ecl general main file upload
        public async Task<ActionResult> Upload(FileModel fm)
        {
            string UploadPath;
            //loading file Progress bar 
            ProgressHub.SendMessage("initializing and preparing", 2);
            //checking file is null
            if (fm.fileName == null)
            {
                return Json(new { statusCode = "200", isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "false" });
            }
            //get file name with out extension
            string FileName = Path.GetFileNameWithoutExtension(fm.fileName.FileName);
            //get file extentoin
            string FileExtension = Path.GetExtension(fm.fileName.FileName);
            //concate file datte with file name
            FileName = DateTime.Now.ToString("yyyyMMdd") + "-" + FileName.Trim() + FileExtension;
            //store temporary in folder
            UploadPath = Path.Combine(Server.MapPath(ConfigurationManager.AppSettings["FileUpload"].ToString()), FileName);
            
            fm.fileName.SaveAs(UploadPath);
            Session["file"] = UploadPath;
            //updating progress bar
            ProgressHub.SendMessage("Validating ", 10);
            // call data validation functon by giving file path 
            string result = await this.DataValidation(UploadPath);
            //checking if result is success
            if (result == "Success")
            {
                //updating progress bar
                ProgressHub.SendMessage("Validating success  and data upload successfully ", 25);
                // run calculation 
                string response = this.ECL_Calculator();
                // delete file
                System.IO.File.Delete(UploadPath);
                //checking response after calculation if success
                if (response == "success")
                {
                    //updating progressbar
                    ProgressHub.SendMessage("Calculation Done Generating Reports", 80);
                    // get data to to generate report
                    var data = this.StagingWiseData();
                    // updating progress bar
                    ProgressHub.SendMessage("Completed", 100);
                    return Json(new { statusCode = "200", staging_data = data, file = "", isValidFile = "true", flag = "", validationStatus = "false" },JsonRequestBehavior.AllowGet);
                }
                // if PD/LGD not porvided 
                else if (response == "PD notRated Empty")
                {
                    return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "nonRated", validationStatus = "true", PDRequired = "true" }, JsonRequestBehavior.AllowGet);
                }
                else if (response == "PD Rated Empty")
                {
                    return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "Rated", validationStatus = "true", PDRequired = "true" }, JsonRequestBehavior.AllowGet);
                }
                else if (response == "PD both Empty")
                {
                    return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "Empty", validationStatus = "true", PDRequired = "true" }, JsonRequestBehavior.AllowGet);
                }
                else if (response == "PD Empty")
                {
                    return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "", validationStatus = "true", PDRequired = "true" }, JsonRequestBehavior.AllowGet);

                }
                else if (response == "LGD Empty")
                {
                    return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "true" }, JsonRequestBehavior.AllowGet);

                }
                else
                {
                    return Json(new { statusCode = "500", file = UploadPath, isValidFile = "false", flag = "", validationStatus = "true" }, JsonRequestBehavior.AllowGet);
                }
            }
            else
            {
                return Json(new { statusCode = "200", file = UploadPath, isValidFile = "false", flag = "", validationStatus = "true", LGDREquired = "false" }, JsonRequestBehavior.AllowGet);
            }
        }


        public FileResult DownloadError()
        {
            string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
            byte[] fileBytes = System.IO.File.ReadAllBytes(fileName);
            string fileNametxt = "Erro Log.xlsx";
            return File(fileBytes, System.Net.Mime.MediaTypeNames.Application.Octet, fileNametxt);
        }
        public FileResult Download()
        {
            string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileTemplate"].ToString());
            byte[] fileBytes = System.IO.File.ReadAllBytes(fileName);
            string fileNametxt = "General Details.xlsx";
            return File(fileBytes, System.Net.Mime.MediaTypeNames.Application.Octet, fileNametxt);
        }

        //PMT formula
        public double PMT(double yearlyInterestRate, int totalNumberOfMonths, double loanAmount, int payment_frequency)
        {      
            var denominator = Math.Pow((1 + yearlyInterestRate), totalNumberOfMonths) - 1;
            return (yearlyInterestRate + (yearlyInterestRate / denominator)) * loanAmount;
        }
        // Return JSON data for Generating Report 
        public JsonResult StagingWiseData()
        {
            //Get User Email
            string userId = Session["UserEmail"].ToString();
            //get Data from output table
            var detail_result = db.OutPuts.Where(x => x.by_user == userId).ToList();
            //get base ,best worst number 
            var result = (from tb in db.Comprehensive_Report.Where(x => x.by_user == userId)
                          select new
                          {
                              tb.Stage,
                              tb.IFRS9_ECL_Base_Number,
                              tb.IFRS9_ECL_Best_Number,
                              tb.FRS9_ECLWorst_Number,
                              tb.EAD,
                          }
                         into x
                          group x by new { x.Stage } into g
                          select new
                          {
                              Stage = g.Min(y => y.Stage),
                              EAD = g.Sum(y => y.EAD),
                              IFRSNumberBase = g.Sum(y => y.IFRS9_ECL_Base_Number),
                              IFRSNumberBest = g.Sum(y => y.IFRS9_ECL_Best_Number),
                              IFRSNumberWorst = g.Sum(y => y.FRS9_ECLWorst_Number),
                          }).OrderBy(z=>z.Stage).ToList();


            //    #region2
            var defaultcode = db.ECL_GeneralInput.Where(x => x.by_user == userId).Select(x => x.PortFolioCode).FirstOrDefault();

            var PortfolioResult = (from tb in db.Comprehensive_Report.Where(x => x.by_user ==userId)
                                   where tb.Portfolio == defaultcode
                                   select new
                                   {
                                       tb.Stage,
                                       tb.IFRS9_ECL_Base_Number,
                                       tb.IFRS9_ECL_Best_Number,
                                       tb.FRS9_ECLWorst_Number,
                                       tb.EAD,
                                       tb.Starting_Exposure,
                                       tb.Portfolio,
                                   }
                       into x
                                   group x by new { x.Stage } into g
                                   select new
                                   {
                                       Portfolio = g.Min(y => y.Portfolio),
                                       Stage = g.Min(y => y.Stage),
                                       EAD = g.Sum(y => y.EAD),
                                       startingExp = g.Sum(y => y.Starting_Exposure),
                                       IFRSNumberBase = g.Sum(y => y.IFRS9_ECL_Base_Number),
                                       IFRSNumberBest = g.Sum(y => y.IFRS9_ECL_Best_Number),
                                       IFRSNumberWorst = g.Sum(y => y.FRS9_ECLWorst_Number),
                                   }).ToList();


            var code = db.ECL_GeneralInput.Where(x => x.by_user ==userId).Select(x => x.PortFolioCode).Distinct();
            var InterimLGD =db.ConsolidatedLGDCalculateds.Where(x => x.by_user == userId).ToList().Count;
            var InterimPD = db.ForwordLooking_PD.Where(x => x.by_user == userId).ToList().Count;
            List<string> forInterim= new List<string>();

            if (InterimLGD != 0 && InterimPD!=0)
            {
                forInterim.Add("LGD Review");
                forInterim.Add( "PD Review");
            }
            else if (InterimLGD != 0)
            {
                forInterim.Add("LGD Review");
            }
            else if (InterimPD!= 0)
            {
                forInterim.Add("PD Review");
            }
            return Json(new { result_data = result, result_two = PortfolioResult, result_portfolio = code ,result_Interim=forInterim }, JsonRequestBehavior.AllowGet);
        }
        //Portfolio Wise Data /2nd table bottom of tha ecl dashboard
        public JsonResult portdata(string val)
        {
            //Get user Id for get  users result
            string userId = Session["UserEmail"].ToString();
            var va = val;

            var PortfolioResult = (from tb in db.Comprehensive_Report.Where(x => x.by_user ==userId)
                                   where tb.Portfolio == val
                                   select new
                                   {
                                       tb.Stage,
                                       tb.IFRS9_ECL_Base_Number,
                                       tb.IFRS9_ECL_Best_Number,
                                       tb.FRS9_ECLWorst_Number,
                                       tb.EAD,
                                       tb.Starting_Exposure,
                                       tb.Portfolio,
                                   }
                     into x
                                   group x by new { x.Stage } into g
                                   select new
                                   {
                                       Portfolio = g.Min(y => y.Portfolio),
                                       Stage = g.Min(y => y.Stage),
                                       EAD = g.Sum(y => y.EAD),
                                       startingExp = g.Sum(y => y.Starting_Exposure),
                                       IFRSNumberBase = g.Sum(y => y.IFRS9_ECL_Base_Number),
                                       IFRSNumberBest = g.Sum(y => y.IFRS9_ECL_Best_Number),
                                       IFRSNumberWorst = g.Sum(y => y.FRS9_ECLWorst_Number),
                                   }).ToList();
            return Json(new { result_two = PortfolioResult, }, JsonRequestBehavior.AllowGet);
        }
        // Facilities file data Validation
        public async Task<ValidateDataObject> DataValidationFacilites(string filename)
        {
            //use excel package for reading file 
            ExcelPackage.LicenseContext = LicenseContext.Commercial;
            FileInfo fi = new FileInfo(filename);
            using (var package = new ExcelPackage(new FileInfo(filename)))
            {
                ExcelWorksheet sheet = package.Workbook.Worksheets.First();
                var start = sheet.Dimension.Start;
                var end = sheet.Dimension.End;

                string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
                var i = DateTime.Now;
                var tasks = new List<Task<ValidateAndDataFacility>>();
                //Call function for data validation for Facility File
              Task<ValidateAndDataFacility>   vaf =  this.ValidatePhaseAsyncFacility(start.Row, end.Row, sheet);
                tasks.Add(vaf);
                var result = await Task.WhenAll(tasks);
                List<string> res = vaf.Result.InsertData;
                ValidateDataObject  vdo= new ValidateDataObject();
                if (ErrorList.Count == 0)
                {
                    vdo.response = "Success";
                    vdo.InsertData = res;
                    return vdo;
                }
                else
                {
                    vdo.response = "Error";
                    vdo.InsertData = res;
                    return vdo;
                }
            }
        }
        /// <summary>
        /// this Fucntion read file using  excel package and validate it by calling ValidatePhaseAsync
        /// </summary>
        /// <param name="filename"></param>
        /// <returns>
        ///  Return Srting which determine that file has passed the validation or not by Success and Error response
        /// </returns>
        public async Task<string> DataValidation(string filename)
        {
            ExcelPackage.LicenseContext = LicenseContext.Commercial;
            FileInfo fi = new FileInfo(filename);
             using (var package = new ExcelPackage(new FileInfo(filename)))
            {
                ExcelWorksheet sheet = package.Workbook.Worksheets.First();
                var start = sheet.Dimension.Start;
                var end = sheet.Dimension.End;

                string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
                List<ECL_GeneralInput> bulk_data = new List<ECL_GeneralInput>();
                var i = DateTime.Now;
                var tasks = new List<Task<ValidateAndData>>();
                tasks.Add(this.ValidatePhaseAsync(start.Row, end.Row, sheet));
                var result = await Task.WhenAll(tasks);
                if (ErrorList.Count == 0)
                {
                    return "Success";
                }
                else
                {
                    return "Error";
                }
            }
        }
 
   
       
        List<myError> ErrorList = new List<myError>();
        List<string> Facilityoutput = new List<string>();
        /// <summary>
        /// this fucntion use for gettiing column index by defining name of the column
        /// </summary>
        /// <param name="ws">Excel File worksheet</param>
        /// <param name="columnNames"></param>
        /// <returns> Reutrn the index of the column i.e. 1,2,3,4</returns>
        /// <exception cref="ArgumentNullException"></exception>
        int GetColumnByName( ExcelWorksheet ws, string columnNames)
        {
            var value = ws.Cells["1:1"].FirstOrDefault(x => x.Text == columnNames.Trim());
            if (value == null)
            {
                string ColumnName = columnNames;
                myError me = new myError();
                me.SerialNumber = 1;
                me.ErrorMessage = "Column "+columnNames+" not Defined";
                me.ErrorInRow = "Column not Defined in first row";
                me.ColumnName = ColumnName;
                ErrorList.Add(me);
                return 0;
            }
            if (ws == null) throw new ArgumentNullException(nameof(ws));
            return ws.Cells["1:1"].FirstOrDefault(c => c.Text == columnNames.Trim()).Start.Column;
        }
        /// <summary>
        /// validate the Multiple Facility File by defining start ,end row and Worksheet of the excel file 
        /// </summary>
        /// <param name="start"> starting point to get row</param>
        /// <param name="end"> end point to get row</param>
        /// <param name="sheet">excel worksheet</param>
        /// <returns> ValidateAndDataFacility object of validated data and Error list  in case of any Error  </returns>
        public async Task<ValidateAndDataFacility> ValidatePhaseAsyncFacility(int start, int end, ExcelWorksheet sheet)
        {
         
            List<string> InsertData = new List<string>();

            var date = DateTime.Now;
            ErrorList.Clear();
            int facility_id = this.GetColumnByName(sheet, "Facility_ID");
            var checkHasList = db.ECL_GeneralInput.Select(x => x.FacilityID).ToList();
            if (ErrorList.Count != 0)
            {
                Session["Errors"] = ErrorList;
                return new ValidateAndDataFacility
                {
                    ErrorList = ErrorList,
                    InsertData = InsertData
                };
            }
            List<string> fac = new List<string>();
            await Task.Run(() =>
            {
                int col = 0;

                for (int row = start; row <= end; row++)
                {
                    ProgressHub.SendMessage("Step 1 Validating and Uploading", row / end * 100);
                    if (row >= 2)
                    {
                        try
                        {
                           
                            col = facility_id;
                            var facilityID = sheet.Cells[row, facility_id].Value;
                            if (facilityID != null && facilityID.ToString().Length <= 100)
                            {
                                if (!(fac.Contains(facilityID.ToString())))
                                {
                                    if (checkHasList.Contains(facilityID.ToString().Trim()))
                                    {
                                        fac.Add(facilityID.ToString());

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Facility ID '"+ facilityID.ToString() + "' not Found.";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    //data.FacilityID = facilityID.ToString();
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Facility ID must be Unique.";
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
                                me.ErrorMessage = "Facility ID Cannot be Blank must be in 100 Characters";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            
                        }
                        catch (Exception ex)
                        {


                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                            myError me = new myError();
                            me.SerialNumber = row;
                            me.ErrorMessage = ex.Message;
                            me.ErrorInRow = sheet.Cells[row, col].Address;
                            me.ColumnName = ColumnName;

                            ErrorList.Add(me);
                        }
                    }

                }
                Session["Errors"] = ErrorList;
              

            });
            return new ValidateAndDataFacility
            {
                ErrorList = ErrorList,
                InsertData = fac
            };

        }
        /// <summary>
        /// validate the General input File by defining start ,end row and Worksheet of the excel file 
        /// </summary>
        /// <param name="start"> starting point to get row</param>
        /// <param name="end"> end point to get row</param>
        /// <param name="sheet">excel worksheet</param>
        /// <returns> ValidateAndDataFacility object of validated data and Error list  in case of any Error  </returns>
        public async Task<ValidateAndData> ValidatePhaseAsync(int start, int end, ExcelWorksheet sheet)
        {
            empty();
            var date = DateTime.Now;
            List<ECL_GeneralInput> bulk_data = new List<ECL_GeneralInput>();
            List<string> InsertData = new List<string>();
            string fileName = Server.MapPath(ConfigurationManager.AppSettings["FileError"].ToString());
            var date2 = DateTime.Now;
            Dictionary<string, List<string>> portcheckValidate = new Dictionary<string, List<string>>();
            List<string> ratedport = new List<string>(){ "SME", "Corporate", "Agri", "Commercial" };
            List<string> nonRated = new List<string>() { "Car Ijarah", "Bike Ijarah", "Commercial Vehicle", "Consumer Ease", "Housing Finance", "Labbaik Financing","Staff"};
            List<string> eratedport = new List<string>() { "AFS", "HTM", "Placements" };
            portcheckValidate.Add("rated", ratedport);
            portcheckValidate.Add("nonrated", nonRated);
            portcheckValidate.Add("eratedport", eratedport);

            ErrorList.Clear();
            int assesmentDate = this.GetColumnByName(sheet, "Assessment_Date");
            int dataimportID = this.GetColumnByName(sheet, "DataImport_ID");
            int extract_date = this.GetColumnByName(sheet, "Extract_Date");
            int facility_id = this.GetColumnByName(sheet, "Facility_ID");
            int customer_id = this.GetColumnByName(sheet, "Customer_ID");
            int portfolio_id = this.GetColumnByName(sheet, "Portfolio_Code");
            int subportolio_code = this.GetColumnByName(sheet, "Subportfolio_Code");
            int product_code = this.GetColumnByName(sheet, "Product_Code");
            int info_flag = this.GetColumnByName(sheet, "Info_Flag");
            int currency = this.GetColumnByName(sheet, "Currency");
            int origination_date = this.GetColumnByName(sheet, "Origination_Date");
            int first_installement_date = this.GetColumnByName(sheet, "First_Installment_Date");
            int days_pass_dues = this.GetColumnByName(sheet, "Past_Due_Dates");
            int original_rating = this.GetColumnByName(sheet, "Original_Rating");
            int current_rating = this.GetColumnByName(sheet, "Current_Rating");
            int base_pd = this.GetColumnByName(sheet, "Base_PD");
            int possitive_pd = this.GetColumnByName(sheet, "Positive_PD");
            int negative_pd = this.GetColumnByName(sheet, "Negative_PD");
            int lgd_rate = this.GetColumnByName(sheet, "LGD_Rate");
            int limit = this.GetColumnByName(sheet, "Limit");
            int drawn_amount = this.GetColumnByName(sheet, "Drawn_Amount");
            int undrawn_amount = this.GetColumnByName(sheet, "Undrawn_Amount");
            int interest_accured = this.GetColumnByName(sheet, "Interest_Accrued");
            int ccf = this.GetColumnByName(sheet, "CCF");
            int impairement_amount = this.GetColumnByName(sheet, "Impairment_Amount");
            int maturity_date = this.GetColumnByName(sheet, "Maturity_Date");
            int eir = this.GetColumnByName(sheet, "EIR");
            int contractual_rate = this.GetColumnByName(sheet, "Contractual_Rate");
            int payment_frequency = this.GetColumnByName(sheet, "Payment_Frequency");
            int stage = this.GetColumnByName(sheet, "Stage");
            int stage_reason = this.GetColumnByName(sheet, "Stage_Reason");
            int nominal_interest_rate = this.GetColumnByName(sheet, "Nominal_Interest_Rate");
            int payment_type_id = this.GetColumnByName(sheet, "Payment_Type_ID");
            int modification_flag = this.GetColumnByName(sheet, "Modification_Flag");
            int modification_value = this.GetColumnByName(sheet, "Modification_Value");
            int written_off_flag = this.GetColumnByName(sheet, "Written_Off_Flag");
            int written_off_value = this.GetColumnByName(sheet, "Written_Off_Value");
            int is_default = this.GetColumnByName(sheet, "Is_Default");
            int classification = this.GetColumnByName(sheet, "Classification");
            int is_watchlist = this.GetColumnByName(sheet, "Is_Watchlist");
            int is_insolvency = this.GetColumnByName(sheet, "Is_Insolvency");
            int hight_risk_industry = this.GetColumnByName(sheet, "High_Risk_Industry");
            int rating_transition = this.GetColumnByName(sheet, "Rating_Transition");
            int additional_flag1 = this.GetColumnByName(sheet, "Additional_Flag1");
            int additional_flag2 = this.GetColumnByName(sheet, "Additional_Flag2");
            int additional_flag3 = this.GetColumnByName(sheet, "Additional_Flag3");
            int collateral_value = this.GetColumnByName(sheet, "Collateral_Value");
            int collateral_desc = this.GetColumnByName(sheet, "Collateral_Description");
            int haircut = this.GetColumnByName(sheet, "Haircut");
            int collateral_benifit = this.GetColumnByName(sheet, "Collateral_Benefit");
            int Rated = this.GetColumnByName(sheet, "Rated/Non-Rated/E-Rated");
            int IsRestructured = this.GetColumnByName(sheet, "IsRestructured");
            int funded = this.GetColumnByName(sheet, "Funded/Non-Funded");
            int acceptances = this.GetColumnByName(sheet, "Acceptances/LC/LG");
            int external_ratings = this.GetColumnByName(sheet, "External Ratings(S&P/Moody)");
            int fvoci = this.GetColumnByName(sheet, "FVOCI_Flag");
            //int product = this.GetColumnByName(sheet, "Product");

            int GOPBacking = this.GetColumnByName(sheet, "GOP Backing");
            if (ErrorList.Count != 0)
            {
                Session["Errors"] = ErrorList;
                return new ValidateAndData
                {
                    bulk_data = bulk_data,
                    ErrorList = ErrorList,
                    InsertData = InsertData
                };
            }
            await Task.Run(()=>
            {
                var db1 = new ifrsEntities();
                int col = 0;
                List<string> fac = new List<string>();

                for (int row = start; row <= end; row++)
                {
                    ProgressHub.SendMessage("Step 1 Validating and Uploading", row / end * 100);
                    if (row >= 2)
                    {
                        try
                        {
                            ECL_GeneralInput data = new ECL_GeneralInput();
                            Guid g = Guid.NewGuid();
                            col = assesmentDate;
                            DateTime asseDate;
                            if (sheet.Cells[row, assesmentDate].Value!=null && DateTime.TryParse(sheet.Cells[row, assesmentDate].Value.ToString(),out asseDate ))
                            {
                                data.AssessmentDate = asseDate;
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage ="Assesment Date is Blank/is not provided in date format(dd/mm//YY).";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;

                                ErrorList.Add(me);
                            }
                            col = extract_date;
                            DateTime extractDate;
                            if (sheet.Cells[row, extract_date].Value != null)
                            {
                                if (DateTime.TryParse(sheet.Cells[row, extract_date].Value.ToString(), out extractDate))
                                {
                                    data.ExtractDate = extractDate;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Extract Date is Blank / is  not provided in date format(dd/mm/YY).";
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
                                me.ErrorMessage = "Extract Date is Blank / is  not provided in date format(dd/mm/YY).";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;

                                ErrorList.Add(me);
                            }


                            col = funded;
                            var fd = sheet.Cells[row, col].Value;
                            if (fd != null)
                            {
                                if (fd.ToString() == "Funded" || fd.ToString() == "Non-Funded")
                                {
                                    data.Funded = fd.ToString();

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "The Faclitiy should be marked as 'Funded' or 'Non-Funded'.";
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
                                me.ErrorMessage = "The Faclitiy should be marked as 'Funded' or 'Non-Funded'.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = acceptances;
                            var accep = sheet.Cells[row, col].Value;
                            byte acp;
                            if (accep != null )
                            {
                                if ((accep.ToString()=="LG"|| accep.ToString() == "Acceptances"|| accep.ToString() == "LC") && data.Funded=="Non-Funded")
                                {

                                    data.Acceptance = accep.ToString();
                                }
                                 else if(data.Funded == "Non-Funded")
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Acceptance/LC/LG must be  'Acceptances' , 'LC' or 'LG' only for Non-Funded Facilities.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                if (data.Funded == "Funded" &&(accep.ToString() == "LG" || accep.ToString() == "Acceptances" || accep.ToString() == "LC"))
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Funded Faclities cannot be 'Acceptances' , 'LC' or 'LG'.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    data.Acceptance = accep.ToString();
                                }
                            }
                            else
                            {
                                if (data.Funded != "Non-Funded")
                                {
                                    data.Acceptance = "";
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Acceptance/LC/LG must not be blank for Non-Funded Facilities.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            data.DataImportID = g.ToString();
                            col = facility_id;
                            var facilityID = sheet.Cells[row, facility_id].Value;
                            if(facilityID!=null && facilityID.ToString().Length <= 100  )
                            {
                                // data.FacilityID = sheet.Cells[row, facility_id].Value.ToString();
                                if (!(fac.Contains(facilityID.ToString())))
                                {
                                    data.FacilityID = facilityID.ToString();
                                    fac.Add(facilityID.ToString());
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Facility ID must be Unique.";
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
                                me.ErrorMessage = "Facility ID Cannot be Blank must be in 100 Characters";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = customer_id;
                            var cust = sheet.Cells[row, customer_id].Value;
                            if (cust !=null && cust.ToString().Length <=100)
                            {
                                data.CustomerID = cust.ToString();
                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Customer ID Cannot be Blank and must be in 100 Characters.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            
                            col = Rated;
                            var rt = sheet.Cells[row, col].Value;
                            if (rt != null)
                            {
                                if (rt.ToString() == "Rated" || rt.ToString() == "Non-Rated"|| rt.ToString() == "E-Rated")
                                {
                                    if (data.Funded=="Non-Funded" && rt.ToString() != "Rated")
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Only Rated facilities can be tagged as Non-Funded";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                        data.Rated = rt.ToString();
                                    }
                                    else
                                    {
                                        data.Rated = rt.ToString();
                                    }

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "The Facility should be marked as 'Rated','E-Rated' or 'Non-Rated'.";
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
                                me.ErrorMessage = "The Facility should be marked as 'Rated','E-Rated' or 'Non-Rated'.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = fvoci;
                            var fv = sheet.Cells[row, col].Value;
                            byte outfv;
                            if (fv != null )
                            {
                                if (byte.TryParse(fv.ToString(),out outfv))
                                {
                                    if (outfv==1||outfv==0)
                                    {
                                        if ((outfv == 1|| outfv == 0) && data.Rated == "E-Rated")
                                        {
                                            data.FVOCI_flag = outfv;
                                        }
                                        else if (outfv == 1 && data.Rated != "E-Rated")
                                        {
                                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                                            myError me = new myError();
                                            me.SerialNumber = row;
                                            me.ErrorMessage = "FVOCI must be 0 or 1 ";
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
                                        me.ErrorMessage = "FVOCI must be 0 or 1 ";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                       
                                }
                            }
                            else
                            {
                                data.FVOCI_flag = 0;
                            }
                            col = external_ratings;
                            var erat = sheet.Cells[row, col].Value;
                            if (erat != null)
                            {
                                if (data.FVOCI_flag==1 )
                                {
                                   var eratings_list= db.External_Ratings.ToList();
                                   var eratings= eratings_list.Where(x=>x.Moody== erat.ToString()||x.SP== erat.ToString()).FirstOrDefault();
                                    if (eratings != null)
                                    {
                                        data.External_Ratings = erat.ToString();
                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "External Ratings must be provided for FVOCI proforma disclosure";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                }
                                else
                                {
                                    data.External_Ratings = erat.ToString();
                                }
                            }
                            else
                            {
                                if (data.FVOCI_flag == 1)
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "External Ratings must be provided for FVOCI proforma disclosure";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    data.External_Ratings = "";
                                }
                            }
                            col = portfolio_id;
                            var portCode= sheet.Cells[row, portfolio_id].Value;
                            if (portCode !=null && data.Rated!=null )
                            {
                                if (data.Rated=="Rated" && portcheckValidate["rated"].Contains(portCode.ToString()))
                                {
                                    data.PortFolioCode = portCode.ToString();
                                }
                                else if (data.Rated == "Non-Rated" && portcheckValidate["nonrated"].Contains(portCode.ToString()))
                                {
                                    data.PortFolioCode = portCode.ToString();
                                }
                                else if (data.Rated == "E-Rated" && portcheckValidate["eratedport"].Contains(portCode.ToString()))
                                {
                                    //   eratedport
                                    if ( sheet.Cells[row, stage].Value == null)
                                    {
                                        string ColumnNams = sheet.Cells[1, col].Value.ToString();
                                        myError mes = new myError();
                                        mes.SerialNumber = row;
                                        mes.ErrorMessage = "Stage must not be blank for " + portCode.ToString();
                                        mes.ErrorInRow = sheet.Cells[row, col].Address;
                                        mes.ColumnName = ColumnNams;
                                        ErrorList.Add(mes);
                                    }
                                    else
                                    {
                                        data.PortFolioCode = portCode.ToString();

                                    }
                                }
                                else
                                {
                                        if (sheet.Cells[row, stage].Value == null)
                                        {
                                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                                            myError me = new myError();
                                            me.SerialNumber = row;
                                            me.ErrorMessage = "Portfolio Code " + portCode.ToString() + " is not tagged as " + data.Rated + " Portfolio";
                                            me.ErrorInRow = sheet.Cells[row, col].Address;
                                            me.ColumnName = ColumnName;
                                            ErrorList.Add(me);
                                        }
                                        else
                                        {
                                            data.PortFolioCode = portCode.ToString();
                                        }
                                     
                                    


                                }

                            }
                            else
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Portfolio Code  "+ portCode.ToString() +" and Rated Cannot be blank !";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = subportolio_code;
                            data.SubPortFolioCode = (sheet.Cells[row, subportolio_code].Value == null) ? ""  : sheet.Cells[row, subportolio_code].Value.ToString();
                            col = product_code;
                            data.ProductCode = (sheet.Cells[row, product_code].Value == null) ? ""  : sheet.Cells[row, product_code].Value.ToString();
                            col = info_flag;
                            if (sheet.Cells[row, info_flag].Value == null)
                            {
                                string ColumnName = sheet.Cells[1, col].Value.ToString();
                                myError me = new myError();
                                me.SerialNumber = row;
                                me.ErrorMessage = "Info Flag Cannot be Blank !";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            else
                            {
                                byte inff;
                                if (byte.TryParse(sheet.Cells[row, info_flag].Value.ToString(),out inff))
                                {
                                    data.InfoFlag = inff;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Info Flag must be in Numeric";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }

                            }
                            col = currency;
                            var currencyy= sheet.Cells[row, currency].Value;
                            if (currencyy !=null )
                            {

                                data.Currency = currencyy.ToString() ;
                            }
                            else
                            {
                                data.Currency = "";
                            }
                            col = origination_date;
                            var origination = sheet.Cells[row, origination_date].Value;
                            DateTime org;
                            if (origination != null)
                            {
                                if (DateTime.TryParse(sheet.Cells[row, origination_date].Value.ToString(), out org))
                                {
                                    data.OriginationDate = org;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Orignation Date must be in Date format(dd/mm/YY).";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.OriginationDate = null;
                            }
                            col = first_installement_date;
                            var firstDate = sheet.Cells[row, first_installement_date].Value;
                            DateTime fdtae;
                            if (firstDate!=null)
                            {
                                if (DateTime.TryParse(firstDate.ToString(),out fdtae))
                                {
                                 
                                    data.FirstInstallmentDate = fdtae;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "First Installment Date must be in Date format(dd/mm/YY)";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);

                                }
                            }
                            else
                            {
                                data.FirstInstallmentDate = null;
                            }
                            
                            //isDefault
                            col = is_default;
                            var DEF = sheet.Cells[row, col].Value;
                            byte def;
                            if (DEF != null)
                            {
                                if (byte.TryParse(DEF.ToString(), out def))
                                {
                                    data.IsDefault = def;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "IsDefault must be in Numeric.";
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
                                me.ErrorMessage = "IsDefault must not blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = days_pass_dues;
                            var pastdueDates = sheet.Cells[row, days_pass_dues].Value;
                            int pdd;
                            if (pastdueDates != null)
                            {
                                if (Int32.TryParse(pastdueDates.ToString(), out pdd))
                                {
                                    if (pdd<0)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "DPD must be a Postive Integer";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else
                                    {
                                        if (data.IsDefault == 0 && pdd > 90)
                                        {
                                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                                            myError me = new myError();
                                            me.SerialNumber = row;
                                            me.ErrorMessage = "Regular Facilities cannot have DPD more than or equal to 90.";
                                            me.ErrorInRow = sheet.Cells[row, col].Address;
                                            me.ColumnName = ColumnName;
                                            ErrorList.Add(me);

                                        }
                                        // Change here and comment
                                        //else if (data.IsDefault == 1 && pdd < 90)
                                        //{
                                        //    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        //    myError me = new myError();
                                        //    me.SerialNumber = row;
                                        //    me.ErrorMessage = "Regular Facilities cannot have DPD more than or equal to 90.";
                                        //    me.ErrorInRow = sheet.Cells[row, col].Address;
                                        //    me.ColumnName = ColumnName;
                                        //    ErrorList.Add(me);
                                        //}
                                        else
                                        {
                                            data.PastDueDays = pdd;
                                        }
                                    }
                                   
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Past Due Date must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.PastDueDays = 0;
                            }
                            //data.PastDueDays = Convert.ToInt32(sheet.Cells[row, days_pass_dues].Value.ToString());
                            col = original_rating;
                            var OR= sheet.Cells[row, original_rating].Value;
                            int or;
                            if (OR!=null)
                            {
                                if (Int32.TryParse(OR.ToString(),out or))
                                {
                                    if (data.IsDefault==0)
                                    {
                                        if (or > 0 && or<=9)
                                        {
                                            data.OriginalRating = or;
                                        }
                                        else
                                        {
                                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                                            myError me = new myError();
                                            me.SerialNumber = row;
                                            me.ErrorMessage = "Original Rating must be greater than 0 less than Equals to 9";
                                            me.ErrorInRow = sheet.Cells[row, col].Address;
                                            me.ColumnName = ColumnName;
                                            ErrorList.Add(me);
                                        }
                                    }
                                    else
                                    {
                                        data.OriginalRating = or;
                                    }
                                   
                            }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Original Rating must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Rated Portfolios must have ratings";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    data.OriginalRating = null;

                                }
                            }
                            //data.OriginalRating = sheet.Cells[row, original_rating].Value.ToString();
                            col = current_rating;
                            var CR = sheet.Cells[row, current_rating].Value;
                            int cr;
                            if (CR != null)
                            {
                                if (Int32.TryParse(CR.ToString(), out cr))
                                {
                                    if (data.IsDefault == 0)
                                    {

                                        if (cr > 0 && cr<=9)
                                        {
                                            data.CurrentRating = cr;
                                        }
                                        else
                                        {
                                            string ColumnName = sheet.Cells[1, col].Value.ToString();
                                            myError me = new myError();
                                            me.SerialNumber = row;
                                            me.ErrorMessage = "Current Rating must be Greater than 0 and less than equals to 9.";
                                            me.ErrorInRow = sheet.Cells[row, col].Address;
                                            me.ColumnName = ColumnName;
                                            ErrorList.Add(me);
                                        }

                                    }
                                    else
                                    {
                                        data.CurrentRating = cr;
                                    }

                            }
                            else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Current Rating must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Rated Portfolios must have ratings";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    data.CurrentRating = null;

                                }
                            }

                          // data.CurrentRating = sheet.Cells[row, current_rating].Value.ToString();
                            data.Transition = (int?)(Convert.ToInt32(data.CurrentRating) - Convert.ToInt32(data.OriginalRating));
                            col = base_pd;
                            var bspd = sheet.Cells[row, base_pd].Value;
                            double bpd;
                            if (bspd !=null)
                            {
                                if (double.TryParse(bspd.ToString(),out bpd))
                                {
                                    if (bpd >= 0 && bpd <= 1)
                                    {
                                        data.BasePD = bpd;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Base PD must in between  0 and 1.";
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
                                    me.ErrorMessage = "Base PD must be in Numeric.";
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
                                    me.ErrorMessage = "Base PD not be null";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                            }
                           // data.BasePD = (sheet.Cells[row, base_pd].Value == null) ? (double?)null : Convert.ToDouble(sheet.Cells[row, base_pd].Value.ToString());
                            col = possitive_pd;
                            var pos = sheet.Cells[row, possitive_pd].Value;
                            double psbest;
                            if (pos!=null)
                            {
                                if (double.TryParse(pos.ToString(),out psbest))
                                {
                                    if (psbest >= 0 && psbest <= 1)
                                    {
                                        data.PositivePDBest = psbest;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Positive PD must be in between 0 and 1.";
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
                                    me.ErrorMessage = "Positive PD must be in Numeric.";
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
                                me.ErrorMessage = "Positive PD must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                           // data.PositivePDBest = (sheet.Cells[row, possitive_pd].Value == null) ? (double?)null : Convert.ToDouble(sheet.Cells[row, possitive_pd].Value.ToString());
                            col = negative_pd;
                            var neg = sheet.Cells[row, negative_pd].Value;
                            double negate;
                            if (neg!=null)
                            {
                                if (double.TryParse(neg.ToString(),out negate))
                                {
                                    if (negate >= 0 && negate <= 1)
                                    {
                                        data.NegativePDWorst = negate;
                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Negative PD must be in between 0 and 1.";
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
                                    me.ErrorMessage = "Negative PD must be in Numeric.";
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
                                me.ErrorMessage = "Negative PD must not be blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = lgd_rate;
                            var LDGrate = sheet.Cells[row, lgd_rate].Value;
                            double lgd;
                            if (LDGrate != null)
                            {
                                if (double.TryParse(LDGrate.ToString(), out lgd))
                                {
                                    if (lgd >= 0 && lgd <= 1)
                                    {
                                        data.LGDRate = lgd;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "LGD Rate must be in between 0 and 1.";
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
                                    me.ErrorMessage = "LGD Rate must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                if (data.Rated=="E-Rated")
                                {
                                    data.LGDRate = 0.45;
                                }
                                else
                                {
                                    data.LGDRate = null;
                                }
                            }
                           
                            col = limit;
                            var lim = sheet.Cells[row, limit].Value;
                            double limi;
                            if (lim != null)
                            {
                                if (double.TryParse(lim.ToString(), out limi))
                                {
                                    if (limi > 0)
                                    {
                                        data.Limit = limi;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Limit must be Greater then zero.";
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
                                    me.ErrorMessage = "Limit must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.Limit = null;
                            }
                           // data.Limit = (sheet.Cells[row, limit].Value == null) ? (double?)null : Convert.ToDouble(sheet.Cells[row, limit].Value);
                            col = drawn_amount;
                            var DRAWN = sheet.Cells[row, drawn_amount].Value;
                            double drwan;
                            if (DRAWN != null)
                            {
                                if (double.TryParse(DRAWN.ToString(), out drwan))
                                {
                                    if (drwan >= 0)
                                    {
                                        data.DrawnAmount = drwan;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Drawn Ammount must be Greater then zero.";
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
                                    me.ErrorMessage = "Drawn Amount must be in Numeric.";
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
                                me.ErrorMessage = "Drawn Ammount must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                           // data.DrawnAmount = Convert.ToDouble(sheet.Cells[row, drawn_amount].Value.ToString());
                            col = undrawn_amount;
                            var UNDRAWN = sheet.Cells[row, undrawn_amount].Value;
                            double undrwan;
                            if (UNDRAWN != null)
                            {
                                if (double.TryParse(UNDRAWN.ToString(), out undrwan))
                                {
                                    if (undrwan >= 0)
                                    {
                                        data.UnDrawnAmount = undrwan;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Undrawn Ammount must be Greater then zero.";
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
                                    me.ErrorMessage = "Undrawn Amount must be in Numeric.";
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
                                me.ErrorMessage = "Undrawn Ammount must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                           // data.UnDrawnAmount = Convert.ToDouble(sheet.Cells[row, undrawn_amount].Value.ToString());
                            col = interest_accured;
                            var inta = sheet.Cells[row, interest_accured].Value;
                            double intacured;
                            if (inta != null)
                            {
                                if (double.TryParse(inta.ToString(), out intacured))
                                {
                                    if (intacured >= 0)
                                    {
                                        data.InterestAccrued = intacured;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Interest Accrued Ammount must be Greater then zero.";
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
                                    me.ErrorMessage = "Interest Accured must be in Numeric.";
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
                                me.ErrorMessage = "Interest Accured must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                           // data.InterestAccrued = Convert.ToDouble(sheet.Cells[row, interest_accured].Value.ToString());
                            col = ccf;
                            var CCF = sheet.Cells[row, ccf].Value;
                            double ccff;
                            if (CCF != null)
                            {
                                if (double.TryParse(CCF.ToString(), out ccff))
                                {
                                    if (ccff >=0 || ccf<=1)
                                    {
                                        data.CCF = ccff;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "CCF must be in between zero and 1.";
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
                                    me.ErrorMessage = "CCF must be in Numeric.";
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
                                me.ErrorMessage = "CFF must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = GOPBacking;
                            int gp;
                            if (sheet.Cells[row, col].Value != null)
                            {
                                if (int.TryParse((sheet.Cells[row, col].Value.ToString()), out gp))
                                {
                                    if (sheet.Cells[row, col].Value == null)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "GOP Backing must be Numeric or not in Alphabat!";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else if (gp>1 || gp<0)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "GOP Backing must be 0 or 1!";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else
                                    {
                                        data.GOP = gp;

                                    }

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "GOP Backing must be Numeric !";
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
                                me.ErrorMessage = "GOP Backing must not be Null!";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }

                            // data.CCF = Convert.ToDouble(sheet.Cells[row, ccf].Value.ToString());
                            // if ead not available then calculate other then same as given
                            if (data.GOP==1)
                            {
                                data.EAD = 0;
                            }
                            else if (data.Funded=="Non-Funded")
                            {

                                data.EAD = (data.DrawnAmount + data.InterestAccrued) * (data.CCF);

                            }
                            else
                            {
                                data.EAD = (data.DrawnAmount + data.InterestAccrued) + (data.UnDrawnAmount * data.CCF);

                            }
                            col = impairement_amount;
                            var IMP = sheet.Cells[row, col].Value;
                            double imp;
                            if (IMP != null)
                            {
                                if (double.TryParse(IMP.ToString(), out imp))
                                {
                                    if (imp > 0)
                                    {
                                        data.ImpairmentAmount = imp;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Impairment_Amount must be greater then zero.";
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
                                    me.ErrorMessage = "Impairment_Amount must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.ImpairmentAmount = null;
                            }
                            
                            col = maturity_date;
                            var MAT = sheet.Cells[row, col].Value;
                            DateTime mat;
                            if (MAT != null)
                            {
                                if (DateTime.TryParse(MAT.ToString(), out mat))
                                {
                                    if ((mat > data.AssessmentDate && (data.IsDefault == 0 || data.IsDefault == 1)) || (mat <= data.AssessmentDate && data.IsDefault==1))
                                    {
                                        data.MaturityDate = mat;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Maturity_Date must be greater than Assesment Date.";
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
                                    me.ErrorMessage = "Maturity_Date must be in Date format(dd/mm/YY)";
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
                                me.ErrorMessage = "Maturity_Date must not be Blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }

                            col = eir;
                            var EIR = sheet.Cells[row, col].Value;
                            double er;
                            if (EIR != null)
                            {
                                if (double.TryParse(EIR.ToString(), out er))
                                {
                                    if (er >=0 && er<=1)
                                    {
                                         data.EIR = er;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "EIR must be in between 0 and 1.";
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
                                    me.ErrorMessage = "EIR must be in Numeric.";
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
                                me.ErrorMessage = "EIR must not blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = contractual_rate;
                            var CON = sheet.Cells[row, col].Value;
                            double con;
                            if (CON != null)
                            {
                                if (double.TryParse(CON.ToString(), out con))
                                {
                                        data.ContractualRate = con;
                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "ContractualRate must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.ContractualRate = 0;
                            }
                           
                            col = payment_frequency;
                            var PAY = sheet.Cells[row, col].Value;
                            int pay;
                            if (PAY != null)
                            {
                                if (Int32.TryParse(PAY.ToString(), out pay))
                                {
                                    if (pay == 0|| pay == 1 || pay == 2 || pay == 4 || pay == 12 )
                                    {
                                        data.PaymentFrequency = pay;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "PaymentFrequency must be 0,1,2,4 and 12.";
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
                                    me.ErrorMessage = "PaymentFrequency be in Numeric.";
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
                                me.ErrorMessage = "PaymentFrequency must not blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                          //  data.PaymentFrequency = Convert.ToInt32(sheet.Cells[row, payment_frequency].Value.ToString());
                            col = nominal_interest_rate;
                            var NOM = sheet.Cells[row, col].Value;
                            double nom;
                            if (NOM != null)
                            {
                                if (double.TryParse(NOM.ToString(), out nom))
                                {
                                    if (nom >= 0 && nom <= 1)
                                    {
                                        data.NominalInterestRate = nom;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "NominalInterestRate must be in between 0 and 1.";
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
                                    me.ErrorMessage = "PaymentFrequency be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.NominalInterestRate = null;
                            }
                            col = payment_type_id;
                            var PT = sheet.Cells[row, col].Value;
                            short pt;
                            if (PT != null)
                            {
                                if (short.TryParse(PT.ToString(), out pt))
                                {
                                        data.PaymentTypeID = pt;

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "PaymentTypeID be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.PaymentTypeID = null;
                            }
                            
                            col = modification_flag;
                            var MOD = sheet.Cells[row, col].Value;
                            if (MOD != null)
                            {
                               
                                    data.MadificationFlag = MOD.ToString();
                            }
                            else
                            {
                                data.MadificationFlag = "";
                            }                        
                            col = modification_value;
                            var MDV = sheet.Cells[row, col].Value;
                            double mdv;
                            if (MDV != null)
                            {
                                if (double.TryParse(MDV.ToString(), out mdv))
                                {
                                    data.ModificationValue = mdv;

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "ModificationValue must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.ModificationValue = null;
                            }
                            col = written_off_flag;
                            data.WrittenOffFlag = (byte?)((sheet.Cells[row, written_off_flag].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, written_off_flag].Value));
                            col = written_off_flag;
                            var WR = sheet.Cells[row, col].Value;
                            double wr;
                            if (WR != null)
                            {
                                if (double.TryParse(WR.ToString(), out wr))
                                {
                                    data.WrittenOffValue = wr;

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "WrittenOffValue must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                data.WrittenOffValue = null;
                            }
                            col = classification;
                            var ccls = sheet.Cells[row, classification].Value;
                            if (ccls!=null)
                            {
                                if (sheet.Cells[row, classification].Value.ToString() == "Regular" || sheet.Cells[row, classification].Value.ToString() == "Substandard" ||
                               sheet.Cells[row, classification].Value.ToString() == "Doubtful" || sheet.Cells[row, classification].Value.ToString() == "Loss" ||
                               sheet.Cells[row, classification].Value.ToString() == "OAEM" || sheet.Cells[row, classification].Value.ToString() == "Standard"
                               )
                                {
                                    if (data.IsDefault==1 &&(sheet.Cells[row, classification].Value.ToString() == "OAEM" || sheet.Cells[row, classification].Value.ToString() == "Substandard" || sheet.Cells[row, classification].Value.ToString() == "Doubtful" || sheet.Cells[row, classification].Value.ToString() == "Loss" ))
                                    {
                                        data.Clasification = sheet.Cells[row, classification].Value.ToString();
                                    }
                                    else if(data.IsDefault == 0)
                                    {
                                        data.Clasification = sheet.Cells[row, classification].Value.ToString();
                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Classification must be Doubtful,OAEM,Substandard,Loss When IsDefault is 1";
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
                                    me.ErrorMessage = "Classification must be Regular,Doubtful,OAEM,Substandard,Loss,Standard ";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                if (data.IsDefault==1)
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Classification must be Doubtful,OAEM,Substandard,Loss ";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                                else
                                {
                                    data.Clasification = "";
                                }
                            }
                           
                            col = is_watchlist;
                            byte wch;
                            if (sheet.Cells[row, is_watchlist].Value != null)
                            {
                                if (byte.TryParse((sheet.Cells[row, is_watchlist].Value.ToString()), out wch))
                                {

                                    if (wch == 1 || wch == 0)
                                    {
                                        data.IsWatchlist = (sheet.Cells[row, is_watchlist].Value == null) ? (byte?)null : wch;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "IsWatchlist must be 0 or 1.";
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
                                    me.ErrorMessage = "IsWatchlist must not Numeric.";
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
                                me.ErrorMessage = "IsWatchlist must not Blank !";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                              
                            col = is_insolvency;
                            data.IsInsolvency = (sheet.Cells[row, is_insolvency].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, is_insolvency].Value);
                            col = hight_risk_industry;
                            data.HighRiskindustry = (byte?)((sheet.Cells[row, hight_risk_industry].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, hight_risk_industry].Value));
                            col = additional_flag3;
                            data.Anyotherflag = (byte?)((sheet.Cells[row, additional_flag3].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, 43].Value));
                            if ((data.PastDueDays > 30 && data.InfoFlag == 2) || (data.PastDueDays > 60 && data.InfoFlag == 1))
                                data.DPD_flag = 1;
                            else
                                data.DPD_flag = 0;
                            col = additional_flag1;
                            data.AdditionalFlag = (byte?)((sheet.Cells[row, additional_flag1].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, additional_flag1].Value));
                            col = additional_flag2;
                            data.AdditionalFlag1 = (byte?)((sheet.Cells[row, additional_flag2].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, additional_flag2].Value));


                            col = additional_flag3;
                            data.AdditionalFlag2 = (byte?)((sheet.Cells[row, additional_flag3].Value == null) ? (byte?)null : Convert.ToByte(sheet.Cells[row, additional_flag2].Value));

                            data.TransitionCheck = 1;
                            if (Convert.ToInt16(data.CurrentRating) >= 7)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 3 && Convert.ToInt16(data.OriginalRating) == 1)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 3 && Convert.ToInt16(data.OriginalRating) == 2)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 2 && Convert.ToInt16(data.OriginalRating) == 3)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 2 && Convert.ToInt16(data.OriginalRating) == 4)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 2 && Convert.ToInt16(data.OriginalRating) == 5)
                                data.TransitionCheck = 1;
                            else if ((Convert.ToInt16(data.CurrentRating) - Convert.ToInt16(data.OriginalRating)) >= 1 && Convert.ToInt16(data.OriginalRating) == 6)
                                data.TransitionCheck = 1;
                            else
                                data.TransitionCheck = 0;


                            col = IsRestructured;
                            byte xc;
                            if (sheet.Cells[row, col].Value!=null)
                            {
                                if (byte.TryParse((sheet.Cells[row, col].Value.ToString()), out xc))
                                {
                                    if (xc<0 || xc>1)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Is_Restructred must be 0 or 1.";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else
                                    {
                                        data.IsRestructured = xc;

                                    }

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Is_Restructred must be Numeric.";
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
                                me.ErrorMessage = "Is_Restructred must not be  blank.";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            




                            col = stage;
                            var stg = sheet.Cells[row, stage].Value;
                            int stageint=0;
                            if (stg !=null)
                            {
                                if (int.TryParse((sheet.Cells[row, col].Value.ToString()), out stageint))
                                {
                                    if (data.IsDefault==1 &&(stageint==1||stageint==2))
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Stage cannot be 1 or 2, when IsDefault flag is 1.";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                   else if (stageint >3)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Stage cannot be greater than 3";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else if (data.IsDefault == 1 && stageint!=3)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Stage must be 3 when isDefault is 1";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else if (data.IsDefault == 0 && stageint == 3)
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Stage must not be 3 when isDefault is 0.";
                                        me.ErrorInRow = sheet.Cells[row, col].Address;
                                        me.ColumnName = ColumnName;
                                        ErrorList.Add(me);
                                    }
                                    else if (data.GOP==1 && stageint!=1)
                                    {
                                        data.Stage = 1;
                                        data.StageReason = "GOP is 1";
                                    }
                                    else
                                    {
                                        data.Stage = (short)stageint;
                                    }

                                }
                                else
                                {
                                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                                    myError me = new myError();
                                    me.SerialNumber = row;
                                    me.ErrorMessage = "Stage must be in Numeric.";
                                    me.ErrorInRow = sheet.Cells[row, col].Address;
                                    me.ColumnName = ColumnName;
                                    ErrorList.Add(me);
                                }
                            }
                            else
                            {
                                List<char> cri = new List<char>();
                                if (data.Rated=="Rated")
                                {
                                    if (((data.OriginalRating == 1 && data.CurrentRating < 4) || (data.OriginalRating == 2 && data.CurrentRating < 5) || (data.OriginalRating == 3 && data.CurrentRating < 5) || (data.OriginalRating == 4 && data.CurrentRating < 6) || (data.OriginalRating == 5 && data.CurrentRating < 7) || (data.OriginalRating == 6 && data.CurrentRating < 7)|| ((data.OriginalRating == 7|| data.OriginalRating == 8|| data.OriginalRating == 9) && data.CurrentRating < 7)) && data.IsDefault != 1 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (data.IsDefault == 1 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 3;
                                        data.StageReason = "Is_Default";
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }


                                    if (data.IsDefault == 0 && data.PastDueDays >= 60 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }
                                    else if (data.IsDefault == 1 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 3;
                                        data.StageReason = "Is_Default";
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    if (data.IsDefault == 0 && data.IsWatchlist == 0 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }
                                    if (data.IsDefault == 0 && data.IsRestructured == 0 && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }
                                    if (data.Stage == null && cri.Contains('Y') && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 2;
                                        data.StageReason = "";
                                    }
                                    else if (data.Stage == null && portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 1;
                                        data.StageReason = "";
                                    }
                                }
                                else if (data.Rated=="Non-Rated")
                                {

                                    //non rated
                                    if (((data.OriginalRating == 1 && data.CurrentRating < 4) || (data.OriginalRating == 2 && data.CurrentRating < 5) || (data.OriginalRating == 3 && data.CurrentRating < 5) || (data.OriginalRating == 4 && data.CurrentRating < 6) || (data.OriginalRating == 5 && data.CurrentRating < 7) || (data.OriginalRating == 6 && data.CurrentRating < 7)) && data.IsDefault != 1 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (data.IsDefault == 1 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 3;
                                        data.StageReason = "Is_Default";
                                    }
                                    else if (portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y'); }
                                    if (data.IsDefault == 0 && data.PastDueDays >= 60 && portcheckValidate["nonrated"].Contains(data.PortFolioCode) && data.PortFolioCode == "Housing Finance")
                                    {
                                        cri.Add('Y');
                                    }
                                    else if (data.IsDefault == 0 && data.PastDueDays >= 30 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }
                                    else if (data.IsDefault == 1 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 3;
                                        data.StageReason = "Is_Default";
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    if (data.IsDefault == 0 && data.IsWatchlist == 0 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (portcheckValidate["rated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }
                                    if (data.IsDefault == 0 && data.IsRestructured == 0 && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('N');
                                    }
                                    else if (portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        cri.Add('Y');
                                    }


                                    if (data.Stage == null && cri.Contains('Y') && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 2;
                                        data.StageReason = "";
                                    }
                                    else if (data.Stage == null && portcheckValidate["nonrated"].Contains(data.PortFolioCode))
                                    {
                                        data.Stage = 1;
                                        data.StageReason = "";
                                    }
                                }
                            }
                            col = collateral_value;
                            data.CollateralValue = (sheet.Cells[row, collateral_value].Value == null) ? 0 : Convert.ToDouble(sheet.Cells[row, collateral_value].Value.ToString());

                            //col = product;
                            //data.CollateralValue = (sheet.Cells[row, collateral_value].Value == null) ? 0 : Convert.ToDouble(sheet.Cells[row, collateral_value].Value.ToString());



                            col = haircut;
                            double hiarout;
                            if (sheet.Cells[row, haircut].Value!=null)
                            {
                                if (double.TryParse(sheet.Cells[row, haircut].Value.ToString(),out hiarout))
                                {
                                    if (hiarout >= 0 && hiarout <=1)
                                    {
                                        data.Haircut = hiarout;

                                    }
                                    else
                                    {
                                        string ColumnName = sheet.Cells[1, col].Value.ToString();
                                        myError me = new myError();
                                        me.SerialNumber = row;
                                        me.ErrorMessage = "Haircut must be in beteween 0 and 1";
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
                                    me.ErrorMessage = "Haircut must be in Numeric";
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
                                me.ErrorMessage = "Haircut cannot be blank";
                                me.ErrorInRow = sheet.Cells[row, col].Address;
                                me.ColumnName = ColumnName;
                                ErrorList.Add(me);
                            }
                            col = collateral_benifit;
                            data.CollateralBenefit = (sheet.Cells[row, collateral_benifit].Value == null) ? (byte?)null : (byte?)(data.CollateralValue * (1 - data.Haircut));
                            data.EAD_Total = (((data.EAD - (data.CollateralValue*(1-data.Haircut)))<0)?0: (data.EAD - (data.CollateralValue * (1 - data.Haircut))));
                            col = collateral_desc;
                            data.CollateralDescription = (sheet.Cells[row, collateral_desc].Value == null) ? "" : sheet.Cells[row, collateral_desc].Value.ToString();
                             data.by_user = Session["UserEmail"].ToString();
                            bulk_data.Add(data);
                        }
                        catch (Exception ex)
                        {


                            string ColumnName = sheet.Cells[1,col].Value.ToString();
                            myError me = new myError();
                            me.SerialNumber = row;
                            me.ErrorMessage = ex.Message;
                            me.ErrorInRow = sheet.Cells[row, col].Address;
                            me.ColumnName = ColumnName;

                            ErrorList.Add(me);
                        }
                    }

                }
                Session["Errors"] = ErrorList;
                try
                {
                    if (bulk_data.Count != 0 && ErrorList.Count == 0)
                    {

                        db1.ECL_GeneralInput.AddRange(bulk_data);
                        db1.SaveChanges();
                    }
                }
                catch (Exception ex)
                {
                    string ColumnName = sheet.Cells[1, col].Value.ToString();
                    myError me = new myError();
                    me.SerialNumber = 1;
                    me.ErrorMessage = ex.Message;
                    me.ErrorInRow = sheet.Cells[1, col].Address;
                    me.ColumnName = ColumnName;

                    ErrorList.Add(me);
                }
                
            });
            return new ValidateAndData
            {
                bulk_data = bulk_data,
                ErrorList = ErrorList,
                InsertData = InsertData
            };
        }
        /// <summary>
        /// Calculate no. of installment and return the installments D
        /// </summary>
        /// <param name="valuationData"> Starting date from where installment starts </param>
        /// <param name="ExpiryDate"> expiry / end date </param>
        /// <param name="pay_freq"> define the installment type yearly,quterly,monthly... etc</param>
        /// <returns> Installment Dates </returns>
        public DateTime[] Years(DateTime valuationData, DateTime ExpiryDate, int pay_freq)
        {

            int P_diff;
            double payments;
            double interval = ((ExpiryDate.Year - valuationData.Year) * 12) + ExpiryDate.Month - valuationData.Month;
            if (pay_freq > 0)
            {
                P_diff = 12 / pay_freq;
                payments = (interval * pay_freq) / 12;
            }
            else
            {
                P_diff = 12;
                payments = (interval / 12);
            }

            int ceiling = (int) Math.Ceiling(payments);
            DateTime[] PaymentDates = new DateTime[ceiling + 1];
            for (int i = 0; i <= ceiling; i++)
            {
                if (i == 0)
                {
                    PaymentDates[i] = valuationData;
                }
                else if (i == ceiling)
                {
                    PaymentDates[i] = ExpiryDate;
                }
                else
                {
                    PaymentDates[i] = valuationData.AddMonths(P_diff * i);
                }
            }
            return PaymentDates;
        }
        /// <summary>
        /// this function calculate the Ecl by calling Stored procedure and check wether LDF or PD is defined for all data or not 
        /// Stored procedure calculate ecl and insert it into Output table
        /// </summary>
        /// <returns> return response that calulation successed or not</returns>
        public string ECL_Calculator()
        {

            ProgressHub.SendMessage("Calculation Starting ", 1);
            string userId = Session["UserEmail"].ToString();
            var inputData = db.ECL_GeneralInput.Where(x => x.by_user == userId).OrderBy(x => x.FacilityID).ToList();
            var countEmptyLGD = inputData.Where(x => x.LGDRate == null && x.by_user == userId).ToList();



            if (countEmptyLGD.Count > 0)
            {
                return "LGD Empty";
            }
            List<OutPut> otpt = new List<OutPut>();
            int coubt = inputData.Count();
            int im = 0;
            foreach (var data in inputData)
            {
                ProgressHub.SendMessage("Step 2 Calculating", Convert.ToInt32(im / coubt * 100));
                #region          
                double year_value = (data.MaturityDate.Year - data.AssessmentDate.Year);
                double pf = (double)data.PaymentFrequency;
                DateTime[] TotalPayements = this.Years(data.AssessmentDate, data.MaturityDate, data.PaymentFrequency);
                double int_rate;

                if (data.PaymentFrequency > 0)
                    int_rate = (double)data.EIR / data.PaymentFrequency;
                else
                    int_rate = data.EIR;

                int num_month = 0;
                if (data.PaymentFrequency == 12)
                {
                    num_month = 1;
                }
                else if (data.PaymentFrequency == 4)
                {
                    num_month = 3;
                }
                else if (data.PaymentFrequency == 2)
                {
                    num_month = 6;
                }
                else if (data.PaymentFrequency == 1)
                {
                    num_month = 12;
                }
                else if (data.PaymentFrequency == 0)
                {
                    num_month = 1;
                }
                string user= Session["UserEmail"].ToString();
                int month_to_add = num_month;
                db.Ecl_Calculation(TotalPayements.Length, year_value, pf, data.EAD_Total, data.EAD, int_rate, data.EIR, month_to_add, data.FacilityID, data.Stage.ToString(), data.AssessmentDate.Date, data.MaturityDate.Date, data.BasePD, data.PositivePDBest, data.NegativePDWorst, data.LGDRate,user);
                

                #endregion
                im++;
            }
            
            var Comprehensive = (from z in db.ECL_GeneralInput.Where(x=>x.by_user==userId).ToList()
                          join f in db.OutPuts.Where(x=>x.by_user== userId).ToList() on z.FacilityID equals f.facilityID
                          select new
                          {
                              z.FacilityID,
                              z.DrawnAmount,
                              z.InterestAccrued,
                              z.PortFolioCode,
                              z.SubPortFolioCode,
                              f.Expected_Loss,
                              f.Expected_Loss_best,
                              f.Expected_Loss_worst,
                              f.ECL_12M,
                              f.Best_ECL_12M,
                              f.worst_ECL_12M,
                              f.Cumulative_Expected_Loss,
                              z.AssessmentDate,
                              z.MaturityDate,
                              z.Stage,
                              z.PaymentFrequency,
                              z.EAD_Total,
                              z.EIR,
                              z.BasePD,
                              z.PositivePDBest,
                              z.NegativePDWorst,
                              z.LGDRate,
                              f.Discount_Factor
                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          {
                              drawnAmount=g.Min(y=>y.DrawnAmount),
                              InterestAccured=g.Min(y=>y.InterestAccrued),
                              FacilityID = g.Min(y => y.FacilityID),
                              Portfolio = g.Min(y => y.PortFolioCode),
                              SubPOrtfolio = g.Min(y => y.SubPortFolioCode),
                              ValuationDate = g.Min(y => y.AssessmentDate),
                              ExpiryDate = g.Min(y => y.MaturityDate),
                              PaymentFrequency = g.Min(y => y.PaymentFrequency),
                              InterestRate = g.Min(y => y.EIR),
                              StartingExposure = g.Min(y => y.DrawnAmount)+g.Min(y=>y.InterestAccrued),
                              EAD = g.Min(y => y.EAD_Total),
                              DiscountRate = g.Min(y => y.Discount_Factor),
                              Pd12MonthBase = g.Min(y => y.BasePD),
                              Pd12MonthBest = g.Min(y => y.PositivePDBest),
                              Pd12MonthWorst = g.Min(y => y.NegativePDWorst),
                              LGD = g.Min(y => y.LGDRate),
                              LECL_Base = g.Sum(y => y.Expected_Loss),
                              LECL_Best = g.Sum(y => y.Expected_Loss_best),
                              LECL_Worst = g.Sum(y => y.Expected_Loss_worst),
                              Ecl_12_months_base = g.Sum(y => y.ECL_12M),
                              Ecl_12_months_best = g.Sum(y => y.Best_ECL_12M),
                              Ecl_12_months_Worst = g.Sum(y => y.worst_ECL_12M),
                              Stage = g.Min(y => y.Stage),

                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) :(g.Min(y => y.Stage) == 1) ? g.Sum(y => y.ECL_12M):g.Min(y=>y.EAD_Total)),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) : (g.Min(y => y.Stage) == 1) ? g.Sum(y => y.Best_ECL_12M): g.Min(y => y.EAD_Total)),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) : (g.Min(y => y.Stage) == 1) ? g.Sum(y => y.worst_ECL_12M): g.Min(y => y.EAD_Total))
                          }).ToList().OrderBy(v => v.FacilityID);
            List<Comprehensive_Report> listcr = new List<Comprehensive_Report>();
            foreach (var item in Comprehensive)
            {
                Comprehensive_Report cr = new Comprehensive_Report();
                cr.Facility_Id = item.FacilityID;
                cr.Portfolio = item.Portfolio;
                cr.SubPortfolio = item.SubPOrtfolio;
                cr.Valuation_date = item.ValuationDate;
                cr.Expiry_Date = item.ExpiryDate;
                cr.Payment_Frequency = item.PaymentFrequency;
                cr.Interest_Rate = item.InterestRate;
                cr.Starting_Exposure = item.StartingExposure;
                cr.Discount_Rate = item.DiscountRate;
                cr.LGD = item.LGD;
                cr.LECL_Base = item.LECL_Base;
                cr.LECL_Best = item.LECL_Best;
                cr.LECL_Worst = item.LECL_Worst;
                cr.Stage = item.Stage;
                cr.by_user= Session["UserEmail"].ToString();
                if (item.Stage == 1)
                {
                    cr.EAD = item.EAD;

                    cr.PD_12_M_Base = item.Pd12MonthBase;
                    cr.PD_12_M_Best = item.Pd12MonthBest;
                    cr.PD_12_M_Worst = item.Pd12MonthWorst;
                    cr.IFRS9_ECL_Base_Number = item.Ecl_12_months_base;
                    cr.IFRS9_ECL_Best_Number = item.Ecl_12_months_best;
                    cr.FRS9_ECLWorst_Number = item.Ecl_12_months_Worst;
                }
                else if (item.Stage == 2)
                {
                    cr.EAD = item.EAD;

                    cr.PD_12_M_Base = item.Pd12MonthBase;
                    cr.PD_12_M_Best = item.Pd12MonthBest;
                    cr.PD_12_M_Worst = item.Pd12MonthWorst;
                    cr.IFRS9_ECL_Base_Number = item.LECL_Base;
                    cr.IFRS9_ECL_Best_Number = item.LECL_Best;
                    cr.FRS9_ECLWorst_Number = item.LECL_Worst;

                }
                else
                {
                    cr.EAD = item.drawnAmount + item.InterestAccured;

                    cr.PD_12_M_Base = 1;
                    cr.PD_12_M_Best = 1;
                    cr.PD_12_M_Worst = 1;
                    cr.IFRS9_ECL_Base_Number =( item.drawnAmount+item.InterestAccured) * 1*item.LGD;
                    cr.IFRS9_ECL_Best_Number = (item.drawnAmount + item.InterestAccured) * 1 * item.LGD;
                    cr.FRS9_ECLWorst_Number = (item.drawnAmount + item.InterestAccured) * 1* item.LGD;
                }

                //cr.IFRS9_ECL_Base_Number = item.IFRSNumberBase;
                //cr.IFRS9_ECL_Best_Number = item.IFRSNumberBest;
                //cr.FRS9_ECLWorst_Number = item.IFRSNumberWorst;
              listcr.Add(cr);
            }
            if (listcr.Count!=0)
            {
                db.Comprehensive_Report.AddRange(listcr);
            db.SaveChanges();
            }
            else
            {
                return "error";
            }
          
            return "success";
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
        /// <summary>
        /// return search by facilty view
        /// </summary>
        /// <returns> Search View</returns>
        public ActionResult Search()
        {
            ViewBag.Message = "Search by facility ID";
            return View("Search");
        }
        /// <summary>
        /// search by facility id 
        /// </summary>
        /// <param name="form">Faclity id </param>
        /// <returns>
        /// View with given facilty data 
        /// </returns>
        [HttpPost]
        public ActionResult SearchResult(FormCollection form)
        {
            string id = form["facilityID"].Trim();
            IList<string> names = id.Split(',').Reverse().ToList<string>();
            ifrsEntities db_entity = new ifrsEntities();
            string userID = Session["UserEmail"].ToString();
            //var data = db.OutPuts.Where(x => x.facilityID == names.Contains(x.facilityID)).ToList();
            var data = (from item in db.OutPuts.Where(x=>x.by_user==userID)
                        where names.Contains(item.facilityID)
                        select item).OrderBy(x=>x.Repayment_Dates).ToList();

            if (data.Count == 0)
            {
                data = null;
            }
            ViewBag.Message ="null";

            return View("Search", data);
        }
        public ActionResult LGD()
        {
            return Redirect("LGD");
        }
        [HttpGet]
        public virtual ActionResult Download(string fileGuid, string fileName)
        {
            if (TempData[fileGuid] != null)
            {
                byte[] data = TempData[fileGuid] as byte[];
                return File(data, "application/vnd.ms-excel", fileName);
            }
            else
            {
                // Problem - Log the error, generate a blank file,
                //           redirect to another controller action - whatever fits with your application
                return new EmptyResult();
            }
        }
        //[HttpPost]
        /// <summary>
        /// this function return lgd review after calculating LGD if not provided and return excel file 
        /// </summary>
        /// <param name="st">key word for checking which report is requested</param>
        public void DownloadReview(string st)
        {
            if (st== "LGD Review")
            {
                ExcelPackage pck = new ExcelPackage();
                var ws = pck.Workbook.Worksheets.Add("LGD");
                ws.Cells["A1"].Value = "Segment";
                ws.Cells["B1"].Value = "Count Of Account";
                ws.Cells["C1"].Value = "Exposure At Default";
                ws.Cells["D1"].Value = "Recoveries";
                ws.Cells["E1"].Value = "Costs";
                ws.Cells["F1"].Value = "Economic Recoveries";
                ws.Cells["G1"].Value = "Economic Costs";
                ws.Cells["H1"].Value = "Recovery Percent";
                ws.Cells["I1"].Value = "Economic Recovery Percent";
                ws.Cells["J1"].Value = "LGD";
                ws.Cells["K1"].Value =  "Economic LGD";
                ws.Cells["A1:Z1"].Style.Font.Bold = true;
                ws.Cells["A1:W1"].AutoFilter = true;
                ws.Cells["A1:W1"].AutoFitColumns();
                int row = 2;
                string userId = Session["UserEmail"].ToString();
                var LGD = db.ConsolidatedLGDCalculateds.Where(x => x.by_user ==userId).ToList();
                
                foreach (var data in LGD)
                {
                    ws.Cells["A" + row].Value = data.Segment;
                    ws.Cells["B" + row].Value = data.CountOfAccount;
                    ws.Cells["C" + row].Value = data.ExposureAtDefault;
                    ws.Cells["D" + row].Value = data.Recoveries;
                    ws.Cells["E" + row].Value = data.Costs;
                    ws.Cells["F" + row].Value = data.EconomicRecoveries;
                    ws.Cells["G" + row].Value = data.EconomicCosts;
                    ws.Cells["H" + row].Value = data.RecoveryPercent;
                    ws.Cells["I" + row].Value = data.EconomicRecoveryPercent;
                    ws.Cells["J" + row].Value = data.LGD;
                    ws.Cells["K" + row].Value = data.EconomicLGD;

                    row++;
                }
                Response.Clear();
                Response.ClearHeaders();
                Response.BinaryWrite(pck.GetAsByteArray());
                Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                Response.AddHeader("content-disposition", "attachment;  filename=LGD Review.xlsx");
                Response.End();

            }
            else if (st== "PD Review")
            {
                ExcelPackage pck = new ExcelPackage();
                var ws = pck.Workbook.Worksheets.Add("Forword_Lookiing_PD");
                ws.Cells["A1"].Value = "Portfolio";
                ws.Cells["B1"].Value = "Ratings";
                ws.Cells["C1"].Value = "TTC";
                ws.Cells["D1"].Value = "Assest_Correlation";
                ws.Cells["E1"].Value = "Base";
                ws.Cells["F1"].Value = "Best";
                ws.Cells["G1"].Value = "Worst";
                ws.Cells["A1:Z1"].Style.Font.Bold = true;
                ws.Cells["A1:W1"].AutoFilter = true;
                ws.Cells["A1:W1"].AutoFitColumns();
                int row = 2;
                string userId = Session["UserEmail"].ToString();
                var FPD = db.ForwordLooking_PD.Where(x => x.by_user == userId).ToList();

                foreach (var data in FPD)
                {
                    ws.Cells["A" + row].Value = data.Portfolio;
                    ws.Cells["B" + row].Value = data.Ratings;
                    ws.Cells["C" + row].Value = data.TTC;
                    ws.Cells["D" + row].Value = data.Assest_Correlation;
                    ws.Cells["E" + row].Value = data.Base;
                    ws.Cells["F" + row].Value = data.Best;
                    ws.Cells["G" + row].Value = data.Worst;

                    row++;
                }
                Response.Clear();
                Response.ClearHeaders();
                Response.BinaryWrite(pck.GetAsByteArray());
                Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                Response.AddHeader("content-disposition", "attachment;  filename=PD Review.xlsx");
                Response.End();
            }
        }
        /// <summary>
        /// return complete excel report of errors
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
            var errors = (from tb in er select new { tb.ColumnName, tb.ErrorInRow, tb.ErrorMessage } into x group x by new { x.ColumnName,x.ErrorMessage}
            into g
            select new { 
                ColumnName=g.Key.ColumnName,
                Count= g.Select(x => x.ErrorInRow).Count(),
                ErrorMessage=g.Key.ErrorMessage,
                ErrorInRow =string.Join(",",g.Select(x=>x.ErrorInRow)),

            }).ToList();
            int i =1;
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
            Response.Clear();
            Response.ClearHeaders();
            Response.BinaryWrite(pck.GetAsByteArray());
            Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            Response.AddHeader("content-disposition", "attachment;  filename=Error ECL.xlsx");
            Response.End();

        }
        /// <summary>
        /// export multiple facility file 
        /// </summary>
        public void ExportMultipleFacilities()
        {
            ExcelPackage pck = new ExcelPackage();
            var ws = pck.Workbook.Worksheets.Add("Multiple Facilities");
            ws.Cells["A1"].Value = "Facility";
            ws.Cells["B1"].Value = "Repayment Dates";
            ws.Cells["C1"].Value = "Days Between";
            ws.Cells["D1"].Value = "Pay Indicator";
            ws.Cells["E1"].Value = "Repayment Amount";
            ws.Cells["F1"].Value = "Days toDiscount";
            ws.Cells["G1"].Value = "PD Cumulative Base";
            ws.Cells["H1"].Value = "PD Cumulative Best";
            ws.Cells["I1"].Value = "PD Cumulative Worst";
            ws.Cells["J1"].Value = "PD Marginal Base";
            ws.Cells["K1"].Value = "PD Marginal Best";
            ws.Cells["L1"].Value = "PD Marginal Worst";
            ws.Cells["M1"].Value = "Discount Factor";
            ws.Cells["N1"].Value = "LGD";

            ws.Cells["O1"].Value = "EAD Start";
            ws.Cells["P1"].Value = "Interest_Paid";
            ws.Cells["Q1"].Value = "EAD";
            ws.Cells["R1"].Value = "Expected Loss Base";
            ws.Cells["S1"].Value = "Expected Loss Best";
            ws.Cells["T1"].Value = "Expected Loss Worst";
            ws.Cells["U1"].Value = "Cumulative Expected Loss Base";
            ws.Cells["V1"].Value = "Cumulative Expected Loss Best";
            ws.Cells["W1"].Value = "Cumulative Expected Loss Worst";
            ws.Cells["X1"].Value = "Selector";
            ws.Cells["Y1"].Value = " Base ECL-12M";
            ws.Cells["Z1"].Value = "Best ECL-12M ";
            ws.Cells["AA1"].Value = "Worst ECL-12M";
            ws.Cells["AB1"].Value = "Cumulative Base ECL-12M";
            ws.Cells["AC1"].Value = "Cumulative Best ECL-12M ";
            ws.Cells["AD1"].Value = "Cumulative Worst ECL-12M";
            string userId = Session["UserEmail"].ToString();
            var outptData = new List<OutPut>();
            List<string> facilss = (List<string>)Session["Facilityoutput"];
            ifrsEntities db = new ifrsEntities();
            var alloutput= db.OutPuts.Where(x => x.by_user == userId).ToList();
            foreach (var facil in facilss)
            {
                string asd = facil.Trim();
                var d = alloutput.Where(x=> x.facilityID==asd).OrderBy(x => x.Repayment_Dates).ToList();
                outptData.AddRange(d);
            }
            

            ws.Cells["A1:AD1"].Style.Font.Bold = true;
            ws.Cells["A1:AD1"].AutoFilter = true;
            ws.Cells["A1:AD1"].AutoFitColumns();
            int row=2;
            foreach (var data in outptData)
            {
                ws.Cells["A" + row].Value = data.facilityID;
                ws.Cells["B"+row].Value = data.Repayment_Dates.ToString(string.Format("dd/MM/yyyy")); ;
                ws.Cells["C" + row].Value = data.Days_Between;
                ws.Cells["D" + row].Value =data.Pay_Indicator;
                ws.Cells["E" + row].Value = data.Repayment_Amount;
                ws.Cells["F" + row].Value = data.DaysToDiscount;
                ws.Cells["G" + row].Value = data.PD_Cumulative;
                ws.Cells["H" + row].Value = data.PD_Cumulative_best;
                ws.Cells["I" + row].Value = data.PD_Cumulative_worst;
                ws.Cells["J" + row].Value = data.PD_Marginal;
                ws.Cells["K" + row].Value = data.PD_Marginal_best;
                ws.Cells["L" + row].Value = data.PD_Marginal_worst;
                ws.Cells["M" + row].Value = data.Discount_Factor;
                ws.Cells["N" + row].Value =data.LGD;

                ws.Cells["O" + row].Value = data.EAD_Start;
                ws.Cells["P" + row].Value = data.Interest_Paid;
                ws.Cells["Q" + row].Value = data.EAD;
                ws.Cells["R" + row].Value = data.Expected_Loss;
                ws.Cells["S" + row].Value = data.Expected_Loss_best;
                ws.Cells["T" + row].Value = data.Expected_Loss_worst;
                ws.Cells["U" + row].Value = data.Cumulative_Expected_Loss;
                ws.Cells["V" + row].Value = data.Cumulative_Expected_Loss_best;
                ws.Cells["W" + row].Value = data.Cumulative_Expected_Loss_worst;
                ws.Cells["X" + row].Value = data.Selector;
                ws.Cells["Y" + row].Value = data.ECL_12M;
                ws.Cells["Z" + row].Value = data.Best_ECL_12M;
                ws.Cells["AA" + row].Value = data.worst_ECL_12M;
                ws.Cells["AB" + row].Value = data.Cumulative_Ecl_12m;
                ws.Cells["AC" + row].Value = data.Cumulative_Ecl_12m_best;
                ws.Cells["AD" + row].Value = data.Cumulative_Ecl_12m_worst;
                row++;
            }
            Response.Clear();
            Response.ClearHeaders();
            Response.BinaryWrite(pck.GetAsByteArray());
            Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            Response.AddHeader("content-disposition", "attachment;  filename=Multiple Facilities.xlsx");
            Response.End();
        }
        /// <summary>
        /// Export Consolidated/comprehensive Report
        /// </summary>
        public void ExportDataTableToExcel()
        {
            ExcelPackage pck = new ExcelPackage();
            var ws = pck.Workbook.Worksheets.Add("Consolidated ECL Calculation");
            ws.Cells["A1"].Value = "Facility ID";
            ws.Cells["B1"].Value = "Portfolio";
            ws.Cells["C1"].Value = "Sub Portfolio";
            ws.Cells["D1"].Value = "Valuation Date";
            ws.Cells["E1"].Value = "Expiry Date";
            ws.Cells["F1"].Value = "Payment Frequency";
            ws.Cells["G1"].Value = "Interest Rate";
            ws.Cells["H1"].Value = "Starting Exposure";
            ws.Cells["I1"].Value = "Discount Rate";
            ws.Cells["J1"].Value = "PD-12-M-Base";
            ws.Cells["K1"].Value = "PD-12-M-Best";
            ws.Cells["L1"].Value = "PD-12-M-Worst";
            ws.Cells["M1"].Value = "LGD";
          
            ws.Cells["N1"].Value = "Assesment Date";
            ws.Cells["O1"].Value = "Data Import ID";
            ws.Cells["P1"].Value = "Extract Date";
            ws.Cells["Q1"].Value = "Customer ID";
            ws.Cells["R1"].Value = "Product Code";
            ws.Cells["S1"].Value = "Info Flag ";
            ws.Cells["T1"].Value = "Currency";
            ws.Cells["U1"].Value = "Origination Date ";
            ws.Cells["V1"].Value = "First Installment Date";
            ws.Cells["W1"].Value = "Past Due date";
            ws.Cells["X1"].Value = " Original_Rating";
            ws.Cells["Y1"].Value = " Current_Rating ";
            ws.Cells["Z1"].Value = " Base_PD";
            ws.Cells["AA1"].Value = " Positive_PD ";
            ws.Cells["AB1"].Value = " Negative_PD ";
            ws.Cells["AC1"].Value = " Drawn_Amount ";
            ws.Cells["AD1"].Value = " Undrawn_Amount";
            ws.Cells["AE1"].Value = " Interest_Accrued ";
            ws.Cells["AF1"].Value = " CCF ";
            ws.Cells["AG1"].Value = "Impairment_Amount";
            ws.Cells["AH1"].Value = "Maturity_Date";
            ws.Cells["AI1"].Value = "EIR";
            ws.Cells["AJ1"].Value = "Contractual_Rate";
            ws.Cells["AK1"].Value = "Stage_Reason";
            ws.Cells["AL1"].Value = "Nominal_Interest_Rate";
            ws.Cells["AM1"].Value = "Payment_Type_ID";
            ws.Cells["AN1"].Value = "Modification_Flag";
            ws.Cells["AO1"].Value = "Modification_Value";
            ws.Cells["AP1"].Value = "Written_Off_Flag";
            ws.Cells["AQ1"].Value = "Written_Off_Value";
            ws.Cells["AR1"].Value = "Is_Default";
            ws.Cells["AS1"].Value = "Classification";
            ws.Cells["AT1"].Value = "Is_Watchlist";
            ws.Cells["AU1"].Value = "Is_Insolvency";
            ws.Cells["AV1"].Value = "High_Risk_Industry";
            ws.Cells["AW1"].Value = "Rating_Transition";
            ws.Cells["AX1"].Value = "Additional_Flag1";
            ws.Cells["AY1"].Value = "Additional_Flag2";
            ws.Cells["AZ1"].Value = "Additional_Flag3";
            ws.Cells["BA1"].Value = "Collateral_Value";
            ws.Cells["BB1"].Value = "Collateral_Description";
            ws.Cells["BC1"].Value = "Haircut";
            ws.Cells["BD1"].Value = "Collateral_Benefit";
            ws.Cells["BE1"].Value = "Rated";
            ws.Cells["BF1"].Value = "IsRestructured";
            ws.Cells["BG1"].Value = "LECL Base";
            ws.Cells["BH1"].Value = "LECL Best";
            ws.Cells["BI1"].Value = "LECL Worst";
            ws.Cells["BJ1"].Value = "ECL-12-M-Base";
            ws.Cells["BK1"].Value = "ECL-12-M-Best";
            ws.Cells["BL1"].Value = "ECL-12-M-Worst";
            ws.Cells["BM1"].Value = "Stage";
            ws.Cells["BN1"].Value = "IFRS9 ECL Base Number";
            ws.Cells["BO1"].Value = "IFRS9 ECL Best Number";
            ws.Cells["BP1"].Value = "IFRS9 ECL Worst Number";
            ws.Cells["BQ1"].Value = "EAD";
            ws.Cells["BR1"].Value = "EAD_Total";
           ws.Cells["BS1"].Value = "Funded/Not-Funded";
           ws.Cells["BT1"].Value = "Acceptances/LC/LG";
           ws.Cells["BU1"].Value = "External Ratings(S&P/Moody)";
           ws.Cells["BV1"].Value = "FVOCI_Flag";
           ws.Cells["BW1"].Value = "GOP Backing";


            string userId = Session["UserEmail"].ToString();
            var detail_result = db.OutPuts.Where(x => x.by_user == userId);
            #region
            var result = (from z in db.ECL_GeneralInput.Where(x => x.by_user == userId).ToList()
                          join f in db.OutPuts.Where(x => x.by_user ==userId).ToList() on z.FacilityID equals f.facilityID
                          select new
                          {
                              z.FacilityID,
                              z.Funded,
                              z.PortFolioCode,
                              z.DataImportID,
                              z.SubPortFolioCode,
                              f.Expected_Loss,
                              f.Expected_Loss_best,
                              f.Expected_Loss_worst,
                              f.ECL_12M,
                              f.Best_ECL_12M,
                              f.worst_ECL_12M,
                              f.Cumulative_Expected_Loss,
                              z.AssessmentDate,
                              z.MaturityDate,
                              z.Stage,
                              z.PaymentFrequency,
                              z.EAD_Total,
                              z.EIR,
                              z.BasePD,
                              z.PositivePDBest,
                              z.NegativePDWorst,
                              z.LGDRate,
                              f.Discount_Factor,
                              z.ExtractDate,
                              z.CustomerID,
                              z.ProductCode,
                              z.InfoFlag,
                              z.Currency,
                              z.OriginationDate,
                              z.FirstInstallmentDate,
                              z.PastDueDays,
                              z.OriginalRating,
                              z.CurrentRating,
                              z.Limit,
                              z.DrawnAmount,
                              z.UnDrawnAmount,
                              z.InterestAccrued,
                              z.CCF,
                              z.ImpairmentAmount,
                              z.StageReason,
                              z.ContractualRate,
                              z.NominalInterestRate,
                              z.PaymentTypeID,
                              z.MadificationFlag,
                              z.ModificationValue,
                              z.WrittenOffFlag,
                              z.WrittenOffValue,
                              z.IsDefault,
                              z.Clasification,
                              z.IsWatchlist,
                              z.IsInsolvency,
                              z.HighRiskindustry,
                              z.Transition,
                              z.AdditionalFlag1,
                              z.AdditionalFlag,
                              z.CollateralValue,
                              z.CollateralDescription,
                              z.Haircut,
                              z.CollateralBenefit,
                              z.Rated,
                              z.IsRestructured,
                              z.AdditionalFlag2,
                              z.EAD,
                              z.Acceptance,
                              z.FVOCI_flag,
                              z.GOP,
                              z.External_Ratings

                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          { 
                              EAD_total=g.Min(y=>y.EAD_Total),
                              EAD = g.Min(y => y.EAD),
                              Funded=g.Min(y=>y.Funded),
                              FacilityID = g.Min(y => y.FacilityID),
                              Portfolio = g.Min(y => y.PortFolioCode),
                              SubPOrtfolio = g.Min(y => y.SubPortFolioCode),
                              ValuationDate = g.Min(y => y.AssessmentDate),
                              ExpiryDate = g.Min(y => y.MaturityDate),
                              PaymentFrequency = g.Min(y => y.PaymentFrequency),
                              InterestRate = g.Min(y => y.EIR),
                              StartingExposure = g.Min(y => y.EAD_Total),
                              DiscountRate = g.Min(y => y.Discount_Factor),
                              Pd12MonthBase = g.Min(y => y.BasePD),
                              Pd12MonthBest = g.Min(y => y.PositivePDBest),
                              Pd12MonthWorst = g.Min(y => y.NegativePDWorst),
                              LGD = g.Min(y => y.LGDRate),
                              LECL_Base = g.Sum(y => y.Expected_Loss),
                              LECL_Best = g.Sum(y => y.Expected_Loss_best),
                              LECL_Worst = g.Sum(y => y.Expected_Loss_worst),
                              Ecl_12_months_base = g.Sum(y => y.ECL_12M),
                              Ecl_12_months_best = g.Sum(y => y.Best_ECL_12M),
                              Ecl_12_months_Worst = g.Sum(y => y.worst_ECL_12M),
                              Stage = g.Min(y => y.Stage),
                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) : g.Sum(y => y.ECL_12M)),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) : g.Sum(y => y.Best_ECL_12M)),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) : g.Sum(y => y.worst_ECL_12M)),
                              Assesment_Date = g.Min(x => x.AssessmentDate),
                              DataImport_ID = g.Min(x => x.DataImportID),
                              Extract_Date = g.Min(x => x.ExtractDate),
                              Customer_Id = g.Min(x => x.CustomerID),
                              Produc_code = g.Min(x => x.ProductCode),
                              Info_Flag = g.Min(x => x.InfoFlag),
                              Currency = g.Min(x => x.Currency),
                              Origination_Date = g.Min(x => x.OriginationDate),
                              First_Installment_Date = g.Min(x => x.FirstInstallmentDate),
                              Past_Due_Dates = g.Min(x => x.PastDueDays),
                              Original_Rating = g.Min(x => x.OriginalRating),
                              Current_Rating = g.Min(x => x.CurrentRating),
                              Base_PD = g.Min(x => x.BasePD),
                              Positive_PD = g.Min(x => x.PositivePDBest),
                              Negative_PD = g.Min(x => x.NegativePDWorst),
                              Limit = g.Min(x => x.Limit),
                              Drawn_Amount = g.Min(x => x.DrawnAmount),
                              Undrawn_Amount = g.Min(x => x.UnDrawnAmount),
                              Interest_Accrued = g.Min(x => x.InterestAccrued),
                              CCF = g.Min(x => x.CCF),
                              Impairment_Amount = g.Min(x => x.ImpairmentAmount),
                              Maturity_Date = g.Min(x => x.MaturityDate),
                              EIR = g.Min(x => x.EIR ),
                              Contractual_Rate = g.Min(x => x.ContractualRate),
                              Stage_Reason = g.Min(x => x.StageReason),
                              Nominal_Interest_Rate = g.Min(x => x.NominalInterestRate),
                              Payment_Type_ID = g.Min(x => x.PaymentTypeID),
                              Modification_Flag = g.Min(x => x.MadificationFlag),
                              Written_Off_Flag = g.Min(x => x.WrittenOffFlag),
                              Written_Off_Value = g.Min(x => x.WrittenOffValue),
                              Is_Default = g.Min(x => x.IsDefault),
                              Classification = g.Min(x => x.Clasification),
                              Modification_Value = g.Min(x => x.ModificationValue),
                              Is_Watchlist = g.Min(x => x.IsWatchlist),
                              Is_Insolvency = g.Min(x => x.IsInsolvency),
                              High_Risk_Industry = g.Min(x => x.HighRiskindustry),
                              Rating_Transition= g.Min(x => x.Transition),
                              Additional_Flag1= g.Min(x => x.AdditionalFlag),
                              Additional_Flag2= g.Min(x => x.AdditionalFlag1),
                              Additional_Flag3 = g.Min(x => x.AdditionalFlag2),
                              Collateral_Value = g.Min(x => x.CollateralValue),
                              Collateral_Description= g.Min(x => x.CollateralDescription),
                              Haircut= g.Min(x => x.Haircut),
                              Collateral_Benefit = g.Min(x => x.CollateralBenefit),
                              Rated = g.Min(x => x.Rated),
                              IsRestructured = g.Min(x => x.IsRestructured),
                              Acceptances=g.Min(x=>x.Acceptance),
                              fvoci=g.Min(x=>x.FVOCI_flag),
                              GOP=g.Min(x=>x.GOP),
                              external_ratings=g.Min(x=>x.External_Ratings)




                          }).ToList().OrderBy(v => v.FacilityID);
            #endregion
            ws.Cells["A1:BW1"].Style.Font.Bold = true;
            ws.Cells["A1:BW1"].AutoFilter = true;
            ws.Cells["A1:BW1"].AutoFitColumns();
            int row = 2;
            foreach (var data in result)
            {
                ws.Cells["A" + row].Value = data.FacilityID;
                ws.Cells["B" + row].Value = data.Portfolio;
                ws.Cells["C" + row].Value = data.SubPOrtfolio;
                //ws.Cells["D" + row].Style.Numberformat.Format = "yyyy-mm-dd";
                ws.Cells["D" + row].Value = data.ValuationDate.ToString(string.Format("dd/MM/yyyy"));
                ws.Cells["E" + row].Value = data.ExpiryDate.ToString(string.Format("dd/MM/yyyy"));
                ws.Cells["F" + row].Value = data.PaymentFrequency;
                ws.Cells["G" + row].Value = data.InterestRate;
                ws.Cells["I" + row].Value = data.DiscountRate;
                ws.Cells["M" + row].Value = data.LGD;
                ws.Cells["N" + row].Value = data.Assesment_Date.ToString(string.Format("dd/MM/yyyy"));
                ws.Cells["O" + row].Value = data.DataImport_ID;
                ws.Cells["P" + row].Value = data.Extract_Date.ToString(string.Format("dd/MM/yyyy"));
                ws.Cells["Q" + row].Value =data.Customer_Id ;
                ws.Cells["R" + row].Value =  data.Produc_code;
                ws.Cells["S" + row].Value =  data.Info_Flag;
                ws.Cells["T" + row].Value =  data.Currency;
                ws.Cells["U" + row].Value = (data.Origination_Date.ToString() == "" ?"" : data.Origination_Date.Value.ToString(string.Format("dd/MM/yyyy")));
                ws.Cells["V" + row].Value = (data.First_Installment_Date.ToString() == "" ? "" : data.First_Installment_Date.Value.ToString(string.Format("dd/MM/yyyy")));
                ws.Cells["W" + row].Value =  data.Past_Due_Dates;
                ws.Cells["X" + row].Value = data.Original_Rating;
                ws.Cells["Y" + row].Value = data.Current_Rating;
                ws.Cells["Z" + row].Value =  data.Base_PD;
                ws.Cells["AA" + row].Value =  data.Positive_PD;
                ws.Cells["AB" + row].Value =  data.Negative_PD;
                ws.Cells["AC" + row].Value =  data.Drawn_Amount;
                ws.Cells["AD" + row].Value =  data.Undrawn_Amount ;
                ws.Cells["AE" + row].Value = data.Interest_Accrued;
                ws.Cells["AF" + row].Value = data.CCF;
                ws.Cells["AG" + row].Value =data.Impairment_Amount;
                ws.Cells["AH" + row].Value = data.Maturity_Date.ToString(string.Format("dd/MM/yyyy"));
                ws.Cells["AI" + row].Value =data.EIR;
                ws.Cells["AJ" + row].Value =  data.Contractual_Rate;
                ws.Cells["AK" + row].Value =  data.Stage_Reason;
                ws.Cells["AL" + row].Value = data.Nominal_Interest_Rate;
                ws.Cells["AM" + row].Value =  data.Payment_Type_ID;
                ws.Cells["AN" + row].Value = data.Modification_Flag;
                ws.Cells["AO" + row].Value =  data.Modification_Value;
                ws.Cells["AP" + row].Value = data.Written_Off_Flag;
                ws.Cells["AQ" + row].Value = data.Written_Off_Value;
                ws.Cells["AR" + row].Value = data.Is_Default;
                ws.Cells["AS" + row].Value =  data.Classification;
                ws.Cells["AT" + row].Value =  data.Is_Watchlist;
                ws.Cells["AU" + row].Value = data.Is_Insolvency;
                ws.Cells["AV" + row].Value = data.High_Risk_Industry;
                ws.Cells["AW" + row].Value = data.Rating_Transition;
                ws.Cells["AX" + row].Value =  data.Additional_Flag1;
                ws.Cells["AY" + row].Value = data.Additional_Flag2;
                ws.Cells["AZ" + row].Value =data.Additional_Flag3;
                ws.Cells["BA" + row].Value = data.Collateral_Value;
                ws.Cells["BB" + row].Value =data.Collateral_Description;
                ws.Cells["BC" + row].Value = data.Haircut;
                ws.Cells["BD" + row].Value = data.Collateral_Benefit;
                ws.Cells["BE" + row].Value = data.Rated;
                ws.Cells["BF" + row].Value = data.IsRestructured;
                
                ws.Cells["BM" + row].Value = data.Stage;
                if (data.Stage == 1)
                {
                    ws.Cells["H" + row].Value = data.StartingExposure;

                    ws.Cells["J" + row].Value = data.Pd12MonthBase;
                    ws.Cells["K" + row].Value = data.Pd12MonthBest;
                    ws.Cells["L" + row].Value = data.Pd12MonthWorst;
                    ws.Cells["BG" + row].Value = data.LECL_Base;
                    ws.Cells["BH" + row].Value = data.LECL_Best;
                    ws.Cells["BI" + row].Value = data.LECL_Worst;
                    ws.Cells["BJ" + row].Value = data.Ecl_12_months_base;
                    ws.Cells["BK" + row].Value = data.Ecl_12_months_best;
                    ws.Cells["BL" + row].Value = data.Ecl_12_months_Worst;
                    ws.Cells["BN" + row].Value = data.Ecl_12_months_base;
                    ws.Cells["BO" + row].Value = data.Ecl_12_months_best;
                    ws.Cells["BP" + row].Value = data.Ecl_12_months_Worst;
                    ws.Cells["BQ" + row].Value = data.EAD;
                    ws.Cells["BR" + row].Value = data.EAD_total;
                }
                else if (data.Stage == 2)
                {
                    ws.Cells["H" + row].Value = data.StartingExposure;

                    ws.Cells["BG" + row].Value = data.LECL_Base;
                    ws.Cells["BH" + row].Value = data.LECL_Best;
                    ws.Cells["BI" + row].Value = data.LECL_Worst;
                    ws.Cells["BJ" + row].Value = data.Ecl_12_months_base;
                    ws.Cells["BK" + row].Value = data.Ecl_12_months_best;
                    ws.Cells["BL" + row].Value = data.Ecl_12_months_Worst;
                    ws.Cells["J" + row].Value = data.Pd12MonthBase;
                    ws.Cells["K" + row].Value = data.Pd12MonthBest;
                    ws.Cells["L" + row].Value = data.Pd12MonthWorst;
                    ws.Cells["BN" + row].Value = data.LECL_Base;
                    ws.Cells["BO" + row].Value = data.LECL_Best;
                    ws.Cells["BP" + row].Value = data.LECL_Worst;
                    ws.Cells["BQ" + row].Value = data.EAD;
                    ws.Cells["BR" + row].Value = data.EAD_total;

                }
                else
                {
                    ws.Cells["H" + row].Value = data.Drawn_Amount + data.Interest_Accrued;

                    ws.Cells["BG" + row].Value = "";
                    ws.Cells["BH" + row].Value ="";
                    ws.Cells["BI" + row].Value = "";
                    ws.Cells["BJ" + row].Value = "";
                    ws.Cells["BK" + row].Value = "";
                    ws.Cells["BL" + row].Value = "";
                    ws.Cells["J" + row].Value = 1;
                    ws.Cells["K" + row].Value = 1;
                    ws.Cells["L" + row].Value = 1;
                    ws.Cells["BN" + row].Value = (data.Drawn_Amount + data.Interest_Accrued) *1*data.LGD;
                    ws.Cells["BO" + row].Value = (data.Drawn_Amount + data.Interest_Accrued) * 1 * data.LGD;
                    ws.Cells["BP" + row].Value = (data.Drawn_Amount + data.Interest_Accrued) * 1 * data.LGD;
                    ws.Cells["BQ" + row].Value = data.Drawn_Amount + data.Interest_Accrued;
                    ws.Cells["BR" + row].Value = data.Drawn_Amount+data.Interest_Accrued;
                }
                    ws.Cells["BS" + row].Value = data.Funded;
                    ws.Cells["BT" + row].Value = data.Acceptances;
                    ws.Cells["BU" + row].Value = data.external_ratings;
                    ws.Cells["BV" + row].Value = data.fvoci;
                    ws.Cells["BW" + row].Value = data.GOP;


                row++;
            }
            //var shape = ws.Drawings.AddShape("Shape1", eShapeStyle.Rect);
            //shape.SetPosition(50, 200);
            //shape.SetSize(200, 100);
            //shape.Text = "Sample 2 outputs the sheet using the Response.BinaryWrite method";
            var filePath = Server.MapPath("~/Content/UploadedFolder");

            var filename = @"REPORT_"+Session["UserEmail"] + DateTime.Now.ToString("dd-MM-yyyy_hh-mm-ss") + ".xlsx";

            string path = Server.MapPath("~/UploadedFolder/" + filename);
            Stream stream = System.IO.File.Create(path);
            pck.SaveAs(stream);
            stream.Close();
            UserRun ur = new UserRun();
            ur.date = DateTime.Now;
            ur.EclReport = path;
            ur.status = "Success";
            ur.UserID = Session["UserEmail"].ToString();
                db.UserRuns.Add(ur);
            db.SaveChanges();
            Response.Clear();
            Response.ClearHeaders();
            Response.BinaryWrite(pck.GetAsByteArray());
            Response.ContentType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            Response.AddHeader("content-disposition", "attachment;  filename=Consolidated ECL.xlsx");
            Response.End();
        }
    }
    public class ValidateAndData
    {
        public List<ECL_GeneralInput> bulk_data { get; set; }
        public List<myError> ErrorList { get; set; }
 
        public List<string> InsertData { get; set; }
    }
    public class ValidateDataObject
    {
        public string response { get; set; }

        public List<string> InsertData { get; set; }
    }
    public class ValidateAndDataFacility
    {
        public List<myError> ErrorList { get; set; }

        public List<string> InsertData { get; set; }
    }
    public class  myError{
        public int? SerialNumber { get; set; }
        public string ErrorMessage { get; set; }
        public string ColumnName { get; set; }
        public string ErrorInRow { get; set; }

        public int? Count { get; set; }

    }
    public static class EpPlusExtensionMethods
    {
        public static int GetColumnByName(this ExcelWorksheet ws, string columnName)
        {
            var value = ws.Cells["1:1"].Where(x => x.Value.ToString() == columnName.Trim());

            if (ws == null) throw new ArgumentNullException(nameof(ws));
            return ws.Cells["1:1"].FirstOrDefault(c => c.Value.ToString() == columnName.Trim()).Start.Column;
        }
    }


    public class jsonPdRetail {

       public int bucket  { get; set; }
       public string portfolio { get; set; }
       public double _base { get; set; }
       public double best { get; set; }
       public double worst { get; set; }


    }


}