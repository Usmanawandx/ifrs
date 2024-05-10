using LMS.Models;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;

namespace LMS.Controllers
{
    [WebAuthorization]

    public class ReportController : Controller
    {
        // GET: Report
        ifrsEntities db = new ifrsEntities();
        public ActionResult Index()
        {
            
            string userId = Session["UserEmail"].ToString();
            #region
            var result = (from z in db.ECL_GeneralInput.Where(x => x.by_user == userId).ToList()
                          join f in db.OutPuts.Where(x => x.by_user == userId).ToList() on z.FacilityID equals f.facilityID
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
                              z.EAD

                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          {
                              EAD_total = g.Min(y => y.EAD_Total),
                              EAD = g.Min(y => y.EAD),
                              Funded = g.Min(y => y.Funded),
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
                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) / 1000 : g.Sum(y => y.ECL_12M) / 1000),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) / 1000 : g.Sum(y => y.Best_ECL_12M) / 1000),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) / 1000 : g.Sum(y => y.worst_ECL_12M) / 1000),
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
                              Drawn_Amount = g.Min(x => x.DrawnAmount) /1000,
                              Undrawn_Amount = g.Min(x => x.UnDrawnAmount),
                              Interest_Accrued = g.Min(x => x.InterestAccrued) / 1000,
                              CCF = g.Min(x => x.CCF),
                              Impairment_Amount = g.Min(x => x.ImpairmentAmount),
                              Maturity_Date = g.Min(x => x.MaturityDate),
                              EIR = g.Min(x => x.EIR),
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
                              Rating_Transition = g.Min(x => x.Transition),
                              Additional_Flag1 = g.Min(x => x.AdditionalFlag),
                              Additional_Flag2 = g.Min(x => x.AdditionalFlag1),
                              Additional_Flag3 = g.Min(x => x.AdditionalFlag2),
                              Collateral_Value = g.Min(x => x.CollateralValue),
                              Collateral_Description = g.Min(x => x.CollateralDescription),
                              Haircut = g.Min(x => x.Haircut),
                              Collateral_Benefit = g.Min(x => x.CollateralBenefit),
                              Rated = g.Min(x => x.Rated),
                              IsRestructured = g.Min(x => x.IsRestructured),
                              stage_3_ecl = (g.Min(y => y.DrawnAmount / 1000) + g.Min(y => y.InterestAccrued / 1000)) * 1 * g.Min(y => y.LGDRate),




                          }).ToList().OrderBy(v => v.FacilityID);
            #endregion
            var funded = result.Where(x => x.Funded == "Funded" && x.Rated!="E-Rated").ToList();
            proforma_advances pa = new proforma_advances();
            //stage 1 column rated performing
            pa.stage_1_rated_high = funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            pa.stage_1_rated_Mudium = funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            pa.stage_1_rated_low = funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 2 column rated performing
            pa.stage_2_rated_high = funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            pa.stage_2_rated_Mudium = funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            pa.stage_2_rated_low = funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 3 column rated performing
            pa.stage_3_rated_high = 0;
            pa.stage_3_rated_Mudium = 0;
            pa.stage_3_rated_low = 0;
            pa.total_high_rated = pa.stage_3_rated_high + pa.stage_2_rated_high + pa.stage_1_rated_high;
            pa.total_Medium_rated = pa.stage_3_rated_Mudium + pa.stage_2_rated_Mudium + pa.stage_1_rated_Mudium;
            pa.total_low_rated = pa.stage_3_rated_low + pa.stage_2_rated_low + pa.stage_1_rated_low;

            //stage 1 column  Non-Rated performing
            pa.stage_1_non_rated_high = funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates < 31).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates < 31).Sum(x => x.Interest_Accrued.Value);
            pa.stage_1_non_rated_medium = funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates > 30 && x.Past_Due_Dates < 61).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates > 30 && x.Past_Due_Dates < 61).Sum(x => x.Interest_Accrued.Value);
            pa.stage_1_non_rated_low= funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates > 60 && x.Past_Due_Dates < 91).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 1 && x.Past_Due_Dates > 61 && x.Past_Due_Dates < 91).Sum(x => x.Interest_Accrued.Value);
            //stage 2 column  Non-Rated performing
            pa.stage_2_non_rated_high = funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates < 31).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates < 31).Sum(x => x.Interest_Accrued.Value);
            pa.stage_2_non_rated_medium = funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates > 30 && x.Past_Due_Dates < 61).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates > 30 && x.Past_Due_Dates < 61).Sum(x => x.Interest_Accrued.Value);
            pa.stage_2_non_rated_low = funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates > 60 && x.Past_Due_Dates < 91).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Rated == "Non-Rated" && x.Stage == 2 && x.Past_Due_Dates > 60 && x.Past_Due_Dates < 91).Sum(x => x.Interest_Accrued.Value);
            //stage 3 column  Non-Rated performing
            pa.stage_3_non_rated_high = 0;
            pa.stage_3_non_rated_medium = 0;
            pa.stage_3_non_rated_low = 0;
            pa.total_high_non_rated = pa.stage_3_non_rated_high + pa.stage_2_non_rated_high + pa.stage_1_non_rated_high;
            pa.total_Medium_non_rated = pa.stage_3_non_rated_medium + pa.stage_2_non_rated_medium + pa.stage_1_non_rated_medium;
            pa.total_low_non_rated = pa.stage_3_non_rated_low + pa.stage_2_non_rated_low + pa.stage_1_non_rated_low;
            //non Performing
            pa.oaem_stage_1 = 0;
            pa.oaem_stage_2 = 0;
            pa.oaem_stage_3 = funded.Where(x => x.Classification == "OAEM" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value)+ funded.Where(x => x.Classification == "OAEM" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            pa.substandard_stage_1 = 0;
            pa.substandard_stage_2 = 0;
            pa.substandard_stage_3 = funded.Where(x => x.Classification == "Substandard" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Classification == "Substandard" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            pa.doubtdul_stage_1 = 0;
            pa.doubtdul_stage_2 = 0;
            pa.doubtdul_stage_3 = funded.Where(x => x.Classification == "Doubtful" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Classification == "Doubtful" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            pa.loss_stage_1 = 0;
            pa.loss_stage_2 = 0;
            pa.loss_stage_3 = funded.Where(x => x.Classification == "Loss" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + funded.Where(x => x.Classification == "Loss" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);

            pa.oaem_total = pa.oaem_stage_1 + pa.oaem_stage_2 + pa.oaem_stage_3;
            pa.substandard_total = pa.substandard_stage_1 + pa.substandard_stage_2 + pa.substandard_stage_3;
            pa.loss_total = pa.loss_stage_1 + pa.loss_stage_2 + pa.loss_stage_3;
            pa.doubtful_total = pa.doubtdul_stage_1 + pa.doubtdul_stage_2 + pa.doubtdul_stage_3;

            pa.stage_1_total = funded.Where(x => x.Stage == 1).Sum(x => x.Drawn_Amount.Value)+ funded.Where(x => x.Stage == 1).Sum(x => x.Interest_Accrued.Value);
            pa.stage_2_total = funded.Where(x => x.Stage == 2).Sum(x => x.Drawn_Amount.Value)+ funded.Where(x => x.Stage == 2).Sum(x => x.Interest_Accrued.Value);
            pa.stage_3_total = funded.Where(x => x.Stage == 3).Sum(x => x.Drawn_Amount.Value)+ funded.Where(x => x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            pa.total_d_i = funded.Sum(x => x.Drawn_Amount.Value) + funded.Sum(x => x.Interest_Accrued.Value);

            pa.ecl_performing_stage_1 = (funded.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBase) * 0.6)+ (funded.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBest) * 0.05)+ (funded.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberWorst) * 0.35);
            pa.ecl_performing_stage_2 = (funded.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBase) * 0.6)+ (funded.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBest) * 0.05)+ (funded.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberWorst) * 0.35);
            pa.ecl_performing_stage_3 = 0;
            pa.ecl_performing_total = pa.ecl_performing_stage_2 + pa.ecl_performing_stage_1;
            pa.ecl_non_performing_stage_1 = 0;
            pa.ecl_non_performing_stage_2 =0;
            pa.ecl_non_performing_stage_3 = funded.Where(x => x.Stage == 3).Sum(x => x.stage_3_ecl.Value); ;
            pa.ecl_non_performing_total = pa.ecl_non_performing_stage_3;

            pa.ecl_stage_1 = pa.ecl_performing_stage_1;
            pa.ecl_stage_2 = pa.ecl_performing_stage_2;
            pa.ecl_stage_3 = pa.ecl_non_performing_stage_3;
            pa.total_ecl = pa.ecl_stage_1 + pa.ecl_stage_2 + pa.ecl_stage_3;
            return View(pa);
        }
        public ActionResult Acceptances()
        {
            string userId = Session["UserEmail"].ToString();
            #region
            var result = (from z in db.ECL_GeneralInput.Where(x => x.by_user == userId && x.Acceptance=="Acceptances").ToList()
                          join f in db.OutPuts.Where(x => x.by_user == userId).ToList() on z.FacilityID equals f.facilityID
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
                              z.EAD

                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          {
                              EAD_total = g.Min(y => y.EAD_Total),
                              EAD = g.Min(y => y.EAD),
                              Funded = g.Min(y => y.Funded),
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
                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) / 1000 : g.Sum(y => y.ECL_12M) / 1000),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) / 1000 : g.Sum(y => y.Best_ECL_12M) / 1000),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) / 1000 : g.Sum(y => y.worst_ECL_12M) / 1000),
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
                              Drawn_Amount = g.Min(x => x.DrawnAmount) / 1000,
                              Undrawn_Amount = g.Min(x => x.UnDrawnAmount),
                              Interest_Accrued = g.Min(x => x.InterestAccrued) / 1000,
                              CCF = g.Min(x => x.CCF),
                              Impairment_Amount = g.Min(x => x.ImpairmentAmount),
                              Maturity_Date = g.Min(x => x.MaturityDate),
                              EIR = g.Min(x => x.EIR),
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
                              Rating_Transition = g.Min(x => x.Transition),
                              Additional_Flag1 = g.Min(x => x.AdditionalFlag),
                              Additional_Flag2 = g.Min(x => x.AdditionalFlag1),
                              Additional_Flag3 = g.Min(x => x.AdditionalFlag2),
                              Collateral_Value = g.Min(x => x.CollateralValue),
                              Collateral_Description = g.Min(x => x.CollateralDescription),
                              Haircut = g.Min(x => x.Haircut),
                              Collateral_Benefit = g.Min(x => x.CollateralBenefit),
                              Rated = g.Min(x => x.Rated),
                              IsRestructured = g.Min(x => x.IsRestructured),





                          }).ToList().OrderBy(v => v.FacilityID);
            #endregion

            Acceptance a = new Acceptance();
            a.stage_1_rated_high = result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.stage_1_rated_Mudium = result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.stage_1_rated_low = result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 2 column rated performing
            a.stage_2_rated_high = result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_rated_Mudium = result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_rated_low = result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 3 column rated performing
            a.stage_3_rated_high = 0;
            a.stage_3_rated_Mudium = 0;
            a.stage_3_rated_low = 0;
            a.total_high_rated = a.stage_3_rated_high + a.stage_2_rated_high + a.stage_1_rated_high;
            a.total_Medium_rated = a.stage_3_rated_Mudium + a.stage_2_rated_Mudium + a.stage_1_rated_Mudium;
            a.total_low_rated = a.stage_3_rated_low + a.stage_2_rated_low + a.stage_1_rated_low;
            a.stage_1_total = result.Where(x => x.Stage == 1).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 1).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_total = result.Where(x => x.Stage == 2).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 2).Sum(x => x.Interest_Accrued.Value);
            a.stage_3_total = 0;
            a.total_d_i = result.Sum(x => x.Drawn_Amount.Value) + result.Sum(x => x.Interest_Accrued.Value);
            a.ecl_performing_stage_1 = (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberWorst) * 0.35);
            a.ecl_performing_stage_2 = (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberWorst) * 0.35);
            a.ecl_performing_stage_3 = 0;
            a.ecl_performing_total = a.ecl_performing_stage_2 + a.ecl_performing_stage_1;
            a.ecl_non_performing_stage_1 = 0;
            a.ecl_non_performing_stage_2 = 0;
            a.ecl_non_performing_stage_3 = 0;
            a.ecl_non_performing_total = 0;

            a.ecl_stage_1 = a.ecl_performing_stage_1;
            a.ecl_stage_2 = a.ecl_performing_stage_2;
            a.ecl_stage_3 = 0;
            a.total_ecl = a.ecl_stage_1 + a.ecl_stage_2 + a.ecl_stage_3;

            return View(a);
        }
        public ActionResult Non_funded()
        {
            string userId = Session["UserEmail"].ToString();
            #region
            var result = (from z in db.ECL_GeneralInput.Where(x => x.by_user == userId && x.Funded== "Non-Funded" &&x.Acceptance != "Acceptances").ToList()
                          join f in db.OutPuts.Where(x => x.by_user == userId).ToList() on z.FacilityID equals f.facilityID
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
                              z.Acceptance

                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          {
                              EAD_total = g.Min(y => y.EAD_Total),
                              EAD = g.Min(y => y.EAD),
                              Funded = g.Min(y => y.Funded),
                              FacilityID = g.Min(y => y.FacilityID),
                              Portfolio = g.Min(y => y.PortFolioCode),
                              SubPOrtfolio = g.Min(y => y.SubPortFolioCode),
                              ValuationDate = g.Min(y => y.AssessmentDate),
                              ExpiryDate = g.Min(y => y.MaturityDate),
                              PaymentFrequency = g.Min(y => y.PaymentFrequency),
                              InterestRate = g.Min(y => y.EIR),
                              Acceptance= g.Min(y=>y.Acceptance),
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
                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) / 1000 : g.Sum(y => y.ECL_12M) / 1000),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) / 1000 : g.Sum(y => y.Best_ECL_12M) / 1000),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) / 1000 : g.Sum(y => y.worst_ECL_12M) / 1000),
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
                              Drawn_Amount = g.Min(x => x.DrawnAmount) / 1000,
                              Undrawn_Amount = g.Min(x => x.UnDrawnAmount),
                              Interest_Accrued = g.Min(x => x.InterestAccrued) / 1000,
                              CCF = g.Min(x => x.CCF),
                              Impairment_Amount = g.Min(x => x.ImpairmentAmount),
                              Maturity_Date = g.Min(x => x.MaturityDate),
                              EIR = g.Min(x => x.EIR),
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
                              Rating_Transition = g.Min(x => x.Transition),
                              Additional_Flag1 = g.Min(x => x.AdditionalFlag),
                              Additional_Flag2 = g.Min(x => x.AdditionalFlag1),
                              Additional_Flag3 = g.Min(x => x.AdditionalFlag2),
                              Collateral_Value = g.Min(x => x.CollateralValue),
                              Collateral_Description = g.Min(x => x.CollateralDescription),
                              Haircut = g.Min(x => x.Haircut),
                              Collateral_Benefit = g.Min(x => x.CollateralBenefit),
                              Rated = g.Min(x => x.Rated),
                              IsRestructured = g.Min(x => x.IsRestructured),





                          }).ToList().OrderBy(v => v.FacilityID);
            #endregion
           var lc = result.Where(x => x.Acceptance == "LC").ToList();
          var  lg = result.Where(x => x.Acceptance == "LG").ToList();
            non_funded a = new non_funded();
            a.stage_1_rated_high = lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.stage_1_rated_Mudium = lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.stage_1_rated_low = lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 2 column rated performing
            a.stage_2_rated_high = lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_rated_Mudium = lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_rated_low = lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + lg.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 3 column rated performing
            a.stage_3_rated_high = 0;
            a.stage_3_rated_Mudium = 0;
            a.stage_3_rated_low = 0;
            a.total_high_rated = a.stage_3_rated_high + a.stage_2_rated_high + a.stage_1_rated_high;
            a.total_Medium_rated = a.stage_3_rated_Mudium + a.stage_2_rated_Mudium + a.stage_1_rated_Mudium;
            a.total_low_rated = a.stage_3_rated_low + a.stage_2_rated_low + a.stage_1_rated_low;
            //letter of credit
            a.letter_stage_1_rated_high = lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.letter_stage_1_rated_Mudium = lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.letter_stage_1_rated_low = lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 1 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 2 column rated performing
            a.letter_stage_2_rated_high = lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating < 4).Sum(x => x.Interest_Accrued.Value);
            a.letter_stage_2_rated_Mudium = lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 3 && x.Current_Rating < 7).Sum(x => x.Interest_Accrued.Value);
            a.letter_stage_2_rated_low = lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Drawn_Amount.Value) + lc.Where(x => x.Rated == "Rated" && x.Stage == 2 && x.Current_Rating > 6 && x.Current_Rating < 10).Sum(x => x.Interest_Accrued.Value);
            //stage 3 column rated performing
            a.letter_stage_3_rated_high = 0;
            a.letter_stage_3_rated_Mudium = 0;
            a.letter_stage_3_rated_low = 0;
            a.letter_total_high_rated = a.stage_3_rated_high + a.stage_2_rated_high + a.stage_1_rated_high;
            a.letter_total_Medium_rated = a.stage_3_rated_Mudium + a.stage_2_rated_Mudium + a.stage_1_rated_Mudium;
            a.letter_total_low_rated = a.stage_3_rated_low + a.stage_2_rated_low + a.stage_1_rated_low;
            // end letter of credit
            a.stage_1_total = result.Where(x => x.Stage == 1).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 1).Sum(x => x.Interest_Accrued.Value);
            a.stage_2_total = result.Where(x => x.Stage == 2).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 2).Sum(x => x.Interest_Accrued.Value);
            a.stage_3_total = 0;
            a.total_d_i = result.Sum(x => x.Drawn_Amount.Value) + result.Sum(x => x.Interest_Accrued.Value);
            a.ecl_performing_stage_1 = (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberWorst) * 0.35);
            a.ecl_performing_stage_2 = (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberWorst) * 0.35);
            a.ecl_performing_stage_3 = 0;
            a.ecl_performing_total = a.ecl_performing_stage_2 + a.ecl_performing_stage_1;
            a.ecl_non_performing_stage_1 = 0;
            a.ecl_non_performing_stage_2 = 0;
            a.ecl_non_performing_stage_3 = 0;
            a.ecl_non_performing_total = 0;

            a.ecl_stage_1 = a.ecl_performing_stage_1;
            a.ecl_stage_2 = a.ecl_performing_stage_2;
            a.ecl_stage_3 = a.ecl_non_performing_stage_3;
            a.total_ecl = a.ecl_stage_1 + a.ecl_stage_2 + a.ecl_stage_3;

            return View(a);
        }
        public ActionResult Investment()
        {
            string userId = Session["UserEmail"].ToString();
            #region
            var result = (from z in db.ECL_GeneralInput.Where(x => x.by_user == userId && x.Rated=="E-Rated"&&x.FVOCI_flag==1).ToList()
                          join f in db.OutPuts.Where(x => x.by_user == userId).ToList() on z.FacilityID equals f.facilityID
                          from exr in db.External_Ratings.ToList() where z.External_Ratings==exr.SP||z.External_Ratings==exr.Moody
                           select new
                          {
                              z.FacilityID,
                              z.Funded,
                              z.External_Ratings,
                              z.PortFolioCode,
                              z.DataImportID,
                              z.SubPortFolioCode,
                              exr.Value,
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
                              z.Acceptance

                          } into x
                          group x by new { x.FacilityID } into g
                          select new
                          {
                              EAD_total = g.Min(y => y.EAD_Total),
                              EAD = g.Min(y => y.EAD),
                              External_ratings=g.Min(y=>y.External_Ratings),
                              Funded = g.Min(y => y.Funded),
                              FacilityID = g.Min(y => y.FacilityID),
                              Portfolio = g.Min(y => y.PortFolioCode),
                              SubPOrtfolio = g.Min(y => y.SubPortFolioCode),
                              ValuationDate = g.Min(y => y.AssessmentDate),
                              ExpiryDate = g.Min(y => y.MaturityDate),
                              External_Ratings_value = g.Min(y=>y.Value),
                              PaymentFrequency = g.Min(y => y.PaymentFrequency),
                              InterestRate = g.Min(y => y.EIR),
                              Acceptance = g.Min(y => y.Acceptance),
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
                              IFRSNumberBase = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss) / 1000 : g.Sum(y => y.ECL_12M) / 1000),
                              IFRSNumberBest = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_best) / 1000 : g.Sum(y => y.Best_ECL_12M) / 1000),
                              IFRSNumberWorst = (g.Min(y => y.Stage) == 2 ? g.Sum(y => y.Expected_Loss_worst) / 1000 : g.Sum(y => y.worst_ECL_12M) / 1000),
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
                              Drawn_Amount = g.Min(x => x.DrawnAmount) / 1000,
                              Undrawn_Amount = g.Min(x => x.UnDrawnAmount),
                              Interest_Accrued = g.Min(x => x.InterestAccrued) / 1000,
                              CCF = g.Min(x => x.CCF),
                              Impairment_Amount = g.Min(x => x.ImpairmentAmount),
                              Maturity_Date = g.Min(x => x.MaturityDate),
                              EIR = g.Min(x => x.EIR),
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
                              Rating_Transition = g.Min(x => x.Transition),
                              Additional_Flag1 = g.Min(x => x.AdditionalFlag),
                              Additional_Flag2 = g.Min(x => x.AdditionalFlag1),
                              Additional_Flag3 = g.Min(x => x.AdditionalFlag2),
                              Collateral_Value = g.Min(x => x.CollateralValue),
                              Collateral_Description = g.Min(x => x.CollateralDescription),
                              Haircut = g.Min(x => x.Haircut),
                              Collateral_Benefit = g.Min(x => x.CollateralBenefit),
                              Rated = g.Min(x => x.Rated),
                              IsRestructured = g.Min(x => x.IsRestructured),
                              stage_3_ecl=((g.Min(y=>y.DrawnAmount)+ g.Min(y => y.InterestAccrued ))*1 * g.Min(y => y.LGDRate))/1000,

                          }).ToList().OrderBy(v => v.FacilityID);
            #endregion
            var ex = db.External_Ratings.ToList();
            investment_proforma ip = new investment_proforma();
            ip.grade_stage_1 = result.Where(x => x.External_Ratings_value>8 && x.Stage==1).ToList().Sum(x=>x.Drawn_Amount.Value)+ result.Where(x => x.External_Ratings_value > 8 && x.Stage == 1).ToList().Sum(x => x.Interest_Accrued.Value);
            ip.grade_stage_2 = result.Where(x => x.External_Ratings_value>8 && x.Stage == 2).ToList().Sum(x=>x.Drawn_Amount.Value)+ result.Where(x => x.External_Ratings_value > 8 && x.Stage == 2).ToList().Sum(x => x.Interest_Accrued.Value);
            ip.grade_stage_3 = 0;
            ip.grade_total = ip.grade_stage_1 + ip.grade_stage_2;
            ip.non_grade_stage_1 = result.Where(x => x.External_Ratings_value <= 8 && x.Stage == 1).ToList().Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.External_Ratings_value <= 8 && x.Stage == 1).ToList().Sum(x => x.Interest_Accrued.Value);
            ip.non_grade_stage_2 = result.Where(x => x.External_Ratings_value <= 8 && x.Stage == 2).ToList().Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.External_Ratings_value <= 8 && x.Stage == 2).ToList().Sum(x => x.Interest_Accrued.Value);
            ip.non_grade_3 = 0;
            ip.non_grade_total = ip.non_grade_stage_1 + ip.non_grade_stage_2;
            ip.substandard_stage_1 = 0;
            ip.substandard_stage_2 = 0;
            ip.substandard_stage_3 = result.Where(x => x.Classification == "Substandard" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Classification == "Substandard" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            ip.doubtful_stage_1 = 0;
            ip.doubtful_stage_2 = 0;
            ip.doubtful_stage_3 = result.Where(x => x.Classification == "Doubtful" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Classification == "Doubtful" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            ip.loss_stage_1 = 0;
            ip.loss_stage_2 = 0;
            ip.loss_stage_3 = result.Where(x => x.Classification == "Loss" && x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Classification == "Loss" && x.Stage == 3).Sum(x => x.Interest_Accrued.Value);

            ip.substandard_stage_total = ip.substandard_stage_3;
            ip.loss_stage_total =  ip.loss_stage_3;
            ip.doubtful_stage_total =ip.doubtful_stage_3;
            ip.stage_1 = result.Where(x => x.Stage == 1).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 1).Sum(x => x.Interest_Accrued.Value);
            ip.stage_2 = result.Where(x => x.Stage == 2).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 2).Sum(x => x.Interest_Accrued.Value);
            ip.stage_3 = result.Where(x => x.Stage == 3).Sum(x => x.Drawn_Amount.Value) + result.Where(x => x.Stage == 3).Sum(x => x.Interest_Accrued.Value);
            ip.total_stages = result.Sum(x => x.Drawn_Amount.Value) + result.Sum(x => x.Interest_Accrued.Value);
            ip.ecl_stage_1 = (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 1).Sum(x => x.IFRSNumberWorst) * 0.35);
            ip.ecl_stage_2 = (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBase) * 0.6) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberBest) * 0.05) + (result.Where(x => x.Stage == 2).Sum(x => x.IFRSNumberWorst) * 0.35);
            ip.ecl_stage_3 = result.Where(x=>x.Stage==3).Sum(x => x.stage_3_ecl.Value);
            ip.ecl_stage_total = ip.ecl_stage_1 + ip.ecl_stage_2+ip.ecl_stage_3;
            return View(ip);
        }
    }
}